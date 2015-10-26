<div style="width:800px;">

    <div class="wc-equipments-buttons">
        <button id="button_filter_all" class="">%all_equip%</button>
        <button onclick="setFilter('own')" class="">%my_reservations%</button>
        <button class="termo" onclick="setFilter('termo')" >Termo</button>
        <button onclick="setFilter('hydro')" class="hydro">Hydro</button>
        <!--<button class="save-btn" onclick="window.location.reload()" title="%save_changes_title%" >%save_changes%</button>-->
    </div>


    <div id='calendar'></div>

</div>

<div id="event_edit_container" style="display:none;">
    <form>

        <input type="hidden" />
        <ul>
            <li>
                <span>%date%: </span><span class="date_holder"></span> 
            </li>
            <li>
                <label for="title">%user_title%: </label><input type="text" name="title" value="%user%" readonly="readonly"  />
                <input type="hidden" name="user" value="%user%"  />
            </li>

            <li>
                <label for="equipment">%equipment%: </label>
                %equipment_select%
            </li>
            <li>
                <label for="start">%start_time%: </label><select name="start"><option value="">%selectbox_start%</option></select>
            </li>

            <li>
                <label for="end">%end_time%: </label><select name="end"><option value="">%selectbox_end%</option></select>
            </li>
        </ul>
    </form>
</div>
</div>