$(document).ready(function() {

	var $calendar = $('#calendar');

	//buttons
	$('#button_filter_all').click(function() {
		$('#calendar').weekCalendar({data: eventData});
		$('#calendar').weekCalendar("refresh");
	});

	$calendar.weekCalendar({
		timeslotsPerHour: 2,
		timeFormat: "H:i",
		dateFormat: "M. j.",
		allowCalEventOverlap: true,
		overlapEventsSeparate: true,
		use24Hour: true,
		firstDayOfWeek: 1,
		businessHours: {start: 8, end: 24, limitDisplay: true},
		daysToShow: 7,
		longDays: days,
		buttonText: {today: buttonToday, lastWeek: buttonLastWeek, nextWeek: buttonNextWeek},
		height: function($calendar) {
			return $(window).height() - $("#dashboard_header").outerHeight() - 20;
		},
		eventRender: function(calEvent, $event) {

			if (calEvent.equipment == "hydro")
				$event.css("backgroundColor", hydro_color);

			if (calEvent.equipment == "termo")
				$event.css("backgroundColor", termo_color);

			if (calEvent.start.getTime() < new Date().getTime()) {
				calEvent.readOnly = true;
				$event.css("backgroundColor", "#21d05f");
				$event.find(".wc-time").css({
					"backgroundColor": "#999",
					"border": "1px solid #888"
				});
			}

			if (calEvent.end.getTime() < new Date().getTime()) {
				calEvent.readOnly = true;
				$event.css("backgroundColor", "#aaa");
				$event.find(".wc-time").css({
					"backgroundColor": "#999",
					"border": "1px solid #888"
				});
			}

		},
		draggable: function(calEvent, $event) {
			return calEvent.readOnly != true;
		},
		resizable: function(calEvent, $event) {
			return calEvent.readOnly != true;
		},
		eventNew: function(calEvent, $event) {
			var $dialogContent = $("#event_edit_container");
			resetForm($dialogContent);
			var startField = $dialogContent.find("select[name='start']").val(calEvent.start);
			var endField = $dialogContent.find("select[name='end']").val(calEvent.end);
			var titleField = $dialogContent.find("input[name='title']").val();
			var equipmentField = $dialogContent.find("select[name='equipment']");
			//predplnime hodnotu prihlasenym uzivatelom -> user globalna premenna
			$dialogContent.find("input[name='title']").val(user);

			$dialogContent.dialog({
				modal: true,
				title: titleAddNewReservation,
				dialogClass: "default-dialog",
				position: ['center', 'center'],
				close: function() {
					$dialogContent.dialog("destroy");
					$dialogContent.hide();
					$('#calendar').weekCalendar("removeUnsavedEvents");
				},
				buttons: [
					{
						text: buttonSaveReservation,
						click: function() {
							var zariadenie = $("select[name='equipment'] option:selected").text();
							var zariadenie_id = $("select[name='equipment'] option:selected").val();
							calEvent.id = id;
							id++;
							calEvent.start = new Date(startField.val());
							calEvent.end = new Date(endField.val());
							calEvent.title = user + " (" + zariadenie + ")";
							calEvent.equipment = zariadenie;
							calEvent.equipment_id = zariadenie_id;

							//nasjkor cekneme ci moze pridat udalost pre tento datum
							$.post(ROOT_PATH + 'includes/modules/reservation/updateCalendar.php', {action: 'check', start: calEvent.start.toString(), end: calEvent.end.toString(), equipment: zariadenie_id, reservation_id: calEvent.id},
							function(data) {
								//ak sme nasli cas v ktorome nemoze byt rezervovane zariadenie
								if (data.status == 1) {
									alert(data.msg);
									window.location.reload();
									//$("#calendar").weekCalendar("refresh"); 
									//$dialogContent.dialog("close");
								}

								if (data.status == 0) {
									$calendar.weekCalendar("removeUnsavedEvents");
									$calendar.weekCalendar("updateEvent", calEvent);
									$.post(ROOT_PATH + 'includes/modules/reservation/updateCalendar.php', {
										action: 'save',
										start: calEvent.start.toString(),
										end: endField.val(),
										equipment: equipmentField.val(),
										reservation_id: calEvent.id,
										title: calEvent.title,
										body: calEvent.body
									});
								}
							}, "json"
								);

							$dialogContent.dialog("close");


						}
					},
					{
						text: buttonCancelReservation,
						click: function() {
							$dialogContent.dialog("close");
						}
					}
				]

			}).show();

			$dialogContent.find(".date_holder").text($calendar.weekCalendar("formatDate", calEvent.start));
			setupStartAndEndTimeFields(startField, endField, calEvent, $calendar.weekCalendar("getTimeslotTimes", calEvent.start));

		},
		eventDrop: function(calEvent, $event) {
			if (calEvent.readOnly) {
				return;
			}

			var $dialogContent = $("#event_edit_container");
			resetForm($dialogContent);
			var startField = $dialogContent.find("select[name='start']").val(calEvent.start);
			var endField = $dialogContent.find("select[name='end']").val(calEvent.end);
			var titleField = $dialogContent.find("input[name='title']").val(calEvent.title);
			var equipmentField = $dialogContent.find("select[name='equipment']").val(calEvent.equipment_id);


			$dialogContent.dialog({
				modal: true,
				dialogClass: "default-dialog",
				title: titleEditReservation + calEvent.title,
				close: function() {
					$dialogContent.dialog("destroy");
					$dialogContent.hide();
					$('#calendar').weekCalendar("removeUnsavedEvents");
				},
				buttons: [
					{
						text: buttonSaveReservation,
						click: function() {

							var zariadenie = $("select[name='equipment'] option:selected").text();
							var zariadenie_id = $("select[name='equipment'] option:selected").val();
							calEvent.start = new Date(startField.val());
							calEvent.end = new Date(endField.val());
							calEvent.title = user + " (" + zariadenie + ")";
							calEvent.equipment = zariadenie;
							calEvent.equipment_id = zariadenie_id;


							//nasjkor cekneme ci moze pridat udalost pre tento datum
							$.post(ROOT_PATH + 'includes/modules/reservation/updateCalendar.php', {action: 'check', start: calEvent.start.toString(), end: calEvent.end.toString(), equipment: zariadenie_id, reservation_id: calEvent.id},
							function(data) {
								//ak sme nasli cas v ktorome nemoze byt rezervovane zariadenie
								if (data.status == 1) {
									alert(data.msg);
									window.location.reload();
									//$("#calendar").weekCalendar("refresh"); 
									//$dialogContent.dialog("close");
								}

								if (data.status == 0) {
									//$calendar.weekCalendar("removeUnsavedEvents");
									$calendar.weekCalendar("updateEvent", calEvent);

									$.post(ROOT_PATH + 'includes/modules/reservation/updateCalendar.php', {
										action: 'update',
										start: calEvent.start.toString(),
										end: endField.val(),
										reservation_id: calEvent.id,
										equipment: equipmentField.val(),
										title: calEvent.title,
										body: calEvent.body
									});
									$dialogContent.dialog("close");
								}
							}, "json"
								);
						}
					},
					{
						text: buttonCancelReservation,
						click: function() {
							$("#calendar").weekCalendar("refresh");
							$dialogContent.dialog("close");
						}

					}
				]

			}).show();

			var startField = $dialogContent.find("select[name='start']").val(calEvent.start);
			var endField = $dialogContent.find("select[name='end']").val(calEvent.end);
			$dialogContent.find(".date_holder").text($calendar.weekCalendar("formatDate", calEvent.start));
			setupStartAndEndTimeFields(startField, endField, calEvent, $calendar.weekCalendar("getTimeslotTimes", calEvent.start));
			$(window).resize().resize(); //fixes a bug in modal overlay size ??
		},
		eventResize: function(calEvent, $event) {
			if (calEvent.readOnly) {
				return;
			}

			var $dialogContent = $("#event_edit_container");
			resetForm($dialogContent);
			var startField = $dialogContent.find("select[name='start']").val(calEvent.start);
			var endField = $dialogContent.find("select[name='end']").val(calEvent.end);
			var titleField = $dialogContent.find("input[name='title']").val(calEvent.title);
			var equipmentField = $dialogContent.find("select[name='equipment']").val(calEvent.equipment_id);


			$dialogContent.dialog({
				modal: true,
				dialogClass: "default-dialog",
				title: titleEditReservation + calEvent.title,
				close: function() {
					$dialogContent.dialog("destroy");
					$dialogContent.hide();
					$('#calendar').weekCalendar("removeUnsavedEvents");
				},
				buttons: [
					{
						text: buttonSaveReservation,
						click: function() {

							var zariadenie = $("select[name='equipment'] option:selected").text();
							var zariadenie_id = $("select[name='equipment'] option:selected").val();
							calEvent.start = new Date(startField.val());
							calEvent.end = new Date(endField.val());
							calEvent.title = user + " (" + zariadenie + ")";
							calEvent.equipment = zariadenie;
							calEvent.equipment_id = zariadenie_id;


							//nasjkor cekneme ci moze pridat udalost pre tento datum
							$.post(ROOT_PATH + 'includes/modules/reservation/updateCalendar.php', {action: 'check', start: calEvent.start.toString(), end: calEvent.end.toString(), equipment: zariadenie_id, reservation_id: calEvent.id},
							function(data) {
								//ak sme nasli cas v ktorome nemoze byt rezervovane zariadenie
								if (data.status == 1) {
									alert(data.msg);
									window.location.reload();
									//$("#calendar").weekCalendar("refresh");
									//$dialogContent.dialog("close");
								}

								if (data.status == 0) {
									//$calendar.weekCalendar("removeUnsavedEvents");
									$calendar.weekCalendar("updateEvent", calEvent);

									$.post(ROOT_PATH + 'includes/modules/reservation/updateCalendar.php', {
										action: 'update',
										start: calEvent.start.toString(),
										end: endField.val(),
										reservation_id: calEvent.id,
										equipment: equipmentField.val(),
										title: calEvent.title,
										body: calEvent.body
									});
									$dialogContent.dialog("close");
								}
							}, "json"
								);

						}
					},
					{
						text: buttonCancelReservation,
						click: function() {
							$("#calendar").weekCalendar("refresh");
							$dialogContent.dialog("close");
						}
					}
				]

			}).show();

			var startField = $dialogContent.find("select[name='start']").val(calEvent.start);
			var endField = $dialogContent.find("select[name='end']").val(calEvent.end);
			$dialogContent.find(".date_holder").text($calendar.weekCalendar("formatDate", calEvent.start));
			setupStartAndEndTimeFields(startField, endField, calEvent, $calendar.weekCalendar("getTimeslotTimes", calEvent.start));
			$(window).resize().resize(); //fixes a bug in modal overlay size ??

		},
		eventClick: function(calEvent, $event) {

			if (calEvent.readOnly) {
				return;
			}

			var $dialogContent = $("#event_edit_container");
			resetForm($dialogContent);
			var startField = $dialogContent.find("select[name='start']").val(calEvent.start);
			var endField = $dialogContent.find("select[name='end']").val(calEvent.end);
			var titleField = $dialogContent.find("input[name='title']").val(calEvent.title);
			var equipmentField = $dialogContent.find("select[name='equipment']").val(calEvent.equipment_id);


			$dialogContent.dialog({
				modal: true,
				dialogClass: "default-dialog",
				title: titleEditReservation + calEvent.title,
				close: function() {
					$dialogContent.dialog("destroy");
					$dialogContent.hide();
					$('#calendar').weekCalendar("removeUnsavedEvents");
				},
				buttons: [
					{
						text: buttonSaveReservation,
						click: function() {
							var zariadenie = $("select[name='equipment'] option:selected").text();
							var zariadenie_id = $("select[name='equipment'] option:selected").val();
							calEvent.start = new Date(startField.val());
							calEvent.end = new Date(endField.val());
							calEvent.title = user + " (" + zariadenie + ")";
							calEvent.equipment = zariadenie;
							calEvent.equipment_id = zariadenie_id;


							//nasjkor cekneme ci moze pridat udalost pre tento datum
							$.post(ROOT_PATH + 'includes/modules/reservation/updateCalendar.php', {action: 'check', start: calEvent.start.toString(), end: calEvent.end.toString(), equipment: zariadenie_id, reservation_id: calEvent.id},
							function(data) {
								//ak sme nasli cas v ktorome nemoze byt rezervovane zariadenie
								if (data.status == 1) {
									alert(data.msg);
									window.location.reload();
									//$("#calendar").weekCalendar("refresh");
									//$dialogContent.dialog("close");
								}

								if (data.status == 0) {
									//$calendar.weekCalendar("removeUnsavedEvents");
									$calendar.weekCalendar("updateEvent", calEvent);

									$.post(ROOT_PATH + 'includes/modules/reservation/updateCalendar.php', {
										action: 'update',
										start: calEvent.start.toString(),
										end: endField.val(),
										reservation_id: calEvent.id,
										equipment: equipmentField.val(),
										title: calEvent.title,
										body: calEvent.body,
										equipment : zariadenie_id
									});
									$dialogContent.dialog("close");
								}
							}, "json"
								);

						}
					},
					{
						text: buttonDeleteReservation,
						click: function() {
							$calendar.weekCalendar("removeEvent", calEvent.id);
							$.post(ROOT_PATH + 'includes/modules/reservation/updateCalendar.php', {
								action: 'delete',
								start: calEvent.start.toString(),
								end: calEvent.end.toString(),
								reservation_id: calEvent.id,
								title: calEvent.title,
								body: calEvent.body
							});
							$dialogContent.dialog("close");
						}
					},
					{
						text: buttonCancelReservation,
						click: function() {
							$dialogContent.dialog("close");
						}
					}
				]

			}).show();

			var startField = $dialogContent.find("select[name='start']").val(calEvent.start);
			var endField = $dialogContent.find("select[name='end']").val(calEvent.end);
			$dialogContent.find(".date_holder").text($calendar.weekCalendar("formatDate", calEvent.start));
			setupStartAndEndTimeFields(startField, endField, calEvent, $calendar.weekCalendar("getTimeslotTimes", calEvent.start));
			$(window).resize().resize(); //fixes a bug in modal overlay size ??

		},
		eventMouseover: function(calEvent, $event) {
		},
		eventMouseout: function(calEvent, $event) {
		},
		noEvents: function() {

		},
		data: eventData
	});

	function resetForm($dialogContent) {
		$dialogContent.find("input").val("");
		$dialogContent.find("textarea").val("");
	}

	function getEventDataFromDatabase() {
		var returnData;

		return $.ajax({
			url: ROOT_PATH + 'includes/modules/reservation/getdata.php'
		});


		/*$.post(ROOT_PATH + 'includes/modules/reservation/getdata.php',function(data) {
		 //alert(data);
		 returnData = data;
		 },"json"
		 ); */



		return {events: returnData};

	}

	function getEventData() {
		var year = new Date().getFullYear();
		var month = new Date().getMonth();
		var day = new Date().getDate();

		return {
			events: [
				{
					"id": 1,
					"start": new Date(year, month, day, 12),
					"end": new Date(year, month, day, 13, 30),
					"title": "Lunch with Mike"
				},
				{
					"id": 2,
					"start": new Date(year, month, day, 14),
					"end": new Date(year, month, day, 14, 45),
					"title": "Dev Meeting"
				},
				{
					"id": 3,
					"start": new Date(year, month, day + 1, 17),
					"end": new Date(year, month, day + 1, 17, 45),
					"title": "Hair cut"
				},
				{
					"id": 4,
					"start": new Date(year, month, day - 1, 8),
					"end": new Date(year, month, day - 1, 9, 30),
					"title": "Team breakfast"
				},
				{
					"id": 5,
					"start": new Date(year, month, day + 1, 14),
					"end": new Date(year, month, day + 1, 15),
					"title": "Product showcase"
				},
				{
					"id": 6,
					"start": new Date(year, month, day, 10),
					"end": new Date(year, month, day, 11),
					"title": "I'm read-only",
					readOnly: true
				}

			]
		};
	}


	/*
	 * Sets up the start and end time fields in the calendar event
	 * form for editing based on the calendar event being edited
	 */
	function setupStartAndEndTimeFields($startTimeField, $endTimeField, calEvent, timeslotTimes) {

		for (var i = 0; i < timeslotTimes.length; i++) {
			var startTime = timeslotTimes[i].start;
			var endTime = timeslotTimes[i].end;
			var startSelected = "";
			if (startTime.getTime() === calEvent.start.getTime()) {
				startSelected = "selected=\"selected\"";
			}
			var endSelected = "";
			if (endTime.getTime() === calEvent.end.getTime()) {
				endSelected = "selected=\"selected\"";
			}
			$startTimeField.append("<option value=\"" + startTime + "\" " + startSelected + ">" + timeslotTimes[i].startFormatted + "</option>");
			$endTimeField.append("<option value=\"" + endTime + "\" " + endSelected + ">" + timeslotTimes[i].endFormatted + "</option>");

		}
		$endTimeOptions = $endTimeField.find("option");
		$startTimeField.trigger("change");
	}


	var $endTimeField = $("select[name='end']");
	var $endTimeOptions = $endTimeField.find("option");

	//reduces the end time options to be only after the start time options.
	$("select[name='start']").change(function() {
		var startTime = $(this).find(":selected").val();
		var currentEndTime = $endTimeField.find("option:selected").val();
		$endTimeField.html(
			$endTimeOptions.filter(function() {
				return startTime < $(this).val();
			})
			);

		var endTimeSelected = false;
		$endTimeField.find("option").each(function() {
			if ($(this).val() === currentEndTime) {
				$(this).attr("selected", "selected");
				endTimeSelected = true;
				return false;
			}
		});

		if (!endTimeSelected) {
			//automatically select an end date 2 slots away.
			$endTimeField.find("option:eq(1)").attr("selected", "selected");
		}

	});




});

function setFilter(equip) {
	$('#calendar').weekCalendar({data: eventDataFilter[equip]});
	$('#calendar').weekCalendar("refresh");
}
