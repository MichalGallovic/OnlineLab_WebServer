<?php

Widget::register('languagePicker', function() {
    return view('localization::languageSwitcher')->render();
});