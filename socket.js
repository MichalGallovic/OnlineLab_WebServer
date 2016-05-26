var server = require('http').Server();
var io = require('socket.io')(server);
var Redis = require('ioredis');
var redis = new Redis();
var sub = new Redis();
server.listen(3000);

var usernames = {};
var videoRooms = [];

Array.prototype.contains = function(obj) {
    var i = this.length;
    while (i--) {
        if (this[i] === obj) {
            return true;
        }
    }
    return false;
}

sub.subscribe('notification-channel');
sub.on('message', function (channel, message) {
    var msg = JSON.parse(message);
    console.log(channel + ':'+ msg.event+msg.data.addressId);
    //console.log("Message decoded: " + JSON.parse(message));
    io.emit(channel + ':'+ msg.event+msg.data.addressId, msg.data.body);
    if(msg.data.video){
        if(videoRooms[msg.data.addressId]){
            videoRooms[msg.data.addressId][msg.data.chatroomId] = msg.data.chatroomName;
        }
        else{
            videoRooms[msg.data.addressId] = {};
            videoRooms[msg.data.addressId][msg.data.chatroomId] = msg.data.chatroomName;
        }
    }
    //io.emit('notification-channel:user'+msg.data.addressId, msg.data.body);
})

io.sockets.on('connection', function (socket) {

    // when receive sdp, broadcast sdp to other user
    socket.on('sdp', function(data){
        console.log('Received SDP from ' + socket.id);
        socket.to(data.room).emit('sdp received', data.sdp);
    });

    // when receive ice candidate, broadcast sdp to other user
    socket.on('ice candidate', function(data){
        console.log('Received ICE candidate from ' + socket.id + ' ' + data.candidate);
        socket.to(data.room).emit('ice candidate received', data.candidate);
    });
/*
    socket.on('message', function (message) {
        log('Got message:', message);
    // for a real app, would be room only (not broadcast)
        socket.broadcast.emit('message', message);
    });
*/
    socket.on('getVideoChatrooms', function (data) {
        io.emit("updateVideoRooms"+data.user_id, videoRooms[data.user_id]);
    });

    socket.on('create or join', function (room) {
        // join room
        var existingRoom = io.sockets.adapter.rooms[room];
        var clients = [];

        if(existingRoom){
            clients = existingRoom;
        }

        if(clients.length == 0){
            socket.join(room);
            socket.video = room;
            io.to(room).emit('empty', room);
        }
        else if(clients.length == 1){
            socket.join(room);
            socket.to(room).emit('joined', room, clients.length + 1);
        }
        // only allow 2 users max per room
        else{
            socket.emit('full', room);
        }
    });

    socket.on('addUser', function (data) {
        socket.user_id = data.user_id;
        socket.user_name = data.user_name;
        socket.room = data.room;

        console.log("meno: " + data.user_name);

        socket.join(data.room);

        if(usernames[data.room]){
            if(!usernames[data.room][data.user_id]){
                usernames[data.room][data.user_id] = data.user_name;
            }
        }else{
            usernames[data.room] = {};
            (usernames[data.room])[data.user_id] = data.user_name;
        }

        io.sockets.to(socket.room).emit('updateUsers', usernames[data.room]);
    });

    socket.on('addMember', function(data) {
        //io.emit('notification-channel:user'+data.user_id, data.body);
        io.sockets.to(socket.room).emit('updateMembers', data.user_id, data.user_name);
        io.sockets.to(socket.room).emit('updateUsers', usernames[socket.room]);
    });

    socket.on('sendChat', function(data) {

        console.log(socket.room);

        console.log(data.body);
        var payload = {body: data.body, chatroom_id: socket.room, user_id: socket.user_id}
        redis.publish('chatroom', JSON.stringify(payload));
        io.sockets.in(socket.room).emit('updateChat', socket.user_name, socket.user_id, data);
        socket.emit('notification-channel:chat'+socket.room);
    });

    socket.on('disconnect', function() {
        //socket.broadcast.emit('updateChat', 'SERVER', socket.username + ' has disconnected');

        if(socket.video){
            for (var object in videoRooms){
                for (var key in videoRooms[object]) {
                    if (key == socket.video){
                        delete videoRooms[object][key];
                    }
                }
            }
        }
        if(socket.room){
            console.log('Left ' + socket.room);
            socket.leave(socket.room);
            delete usernames[socket.room][socket.user_id];
            io.sockets.to(socket.room).emit('updateUsers', (usernames[socket.room]));//io.sockets.clients(socket.room)
        }
    });

});