<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Translation Manager</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <script>//https://github.com/rails/jquery-ujs/blob/master/src/rails.js
        (function(e,t){if(e.rails!==t){e.error("jquery-ujs has already been loaded!")}var n;var r=e(document);e.rails=n={linkClickSelector:"a[data-confirm], a[data-method], a[data-remote], a[data-disable-with]",buttonClickSelector:"button[data-remote], button[data-confirm]",inputChangeSelector:"select[data-remote], input[data-remote], textarea[data-remote]",formSubmitSelector:"form",formInputClickSelector:"form input[type=submit], form input[type=image], form button[type=submit], form button:not([type])",disableSelector:"input[data-disable-with], button[data-disable-with], textarea[data-disable-with]",enableSelector:"input[data-disable-with]:disabled, button[data-disable-with]:disabled, textarea[data-disable-with]:disabled",requiredInputSelector:"input[name][required]:not([disabled]),textarea[name][required]:not([disabled])",fileInputSelector:"input[type=file]",linkDisableSelector:"a[data-disable-with]",buttonDisableSelector:"button[data-remote][data-disable-with]",CSRFProtection:function(t){var n=e('meta[name="csrf-token"]').attr("content");if(n)t.setRequestHeader("X-CSRF-Token",n)},refreshCSRFTokens:function(){var t=e("meta[name=csrf-token]").attr("content");var n=e("meta[name=csrf-param]").attr("content");e('form input[name="'+n+'"]').val(t)},fire:function(t,n,r){var i=e.Event(n);t.trigger(i,r);return i.result!==false},confirm:function(e){return confirm(e)},ajax:function(t){return e.ajax(t)},href:function(e){return e.attr("href")},handleRemote:function(r){var i,s,o,u,a,f,l,c;if(n.fire(r,"ajax:before")){u=r.data("cross-domain");a=u===t?null:u;f=r.data("with-credentials")||null;l=r.data("type")||e.ajaxSettings&&e.ajaxSettings.dataType;if(r.is("form")){i=r.attr("method");s=r.attr("action");o=r.serializeArray();var h=r.data("ujs:submit-button");if(h){o.push(h);r.data("ujs:submit-button",null)}}else if(r.is(n.inputChangeSelector)){i=r.data("method");s=r.data("url");o=r.serialize();if(r.data("params"))o=o+"&"+r.data("params")}else if(r.is(n.buttonClickSelector)){i=r.data("method")||"get";s=r.data("url");o=r.serialize();if(r.data("params"))o=o+"&"+r.data("params")}else{i=r.data("method");s=n.href(r);o=r.data("params")||null}c={type:i||"GET",data:o,dataType:l,beforeSend:function(e,i){if(i.dataType===t){e.setRequestHeader("accept","*/*;q=0.5, "+i.accepts.script)}if(n.fire(r,"ajax:beforeSend",[e,i])){r.trigger("ajax:send",e)}else{return false}},success:function(e,t,n){r.trigger("ajax:success",[e,t,n])},complete:function(e,t){r.trigger("ajax:complete",[e,t])},error:function(e,t,n){r.trigger("ajax:error",[e,t,n])},crossDomain:a};if(f){c.xhrFields={withCredentials:f}}if(s){c.url=s}return n.ajax(c)}else{return false}},handleMethod:function(r){var i=n.href(r),s=r.data("method"),o=r.attr("target"),u=e("meta[name=csrf-token]").attr("content"),a=e("meta[name=csrf-param]").attr("content"),f=e('<form method="post" action="'+i+'"></form>'),l='<input name="_method" value="'+s+'" type="hidden" />';if(a!==t&&u!==t){l+='<input name="'+a+'" value="'+u+'" type="hidden" />'}if(o){f.attr("target",o)}f.hide().append(l).appendTo("body");f.submit()},formElements:function(t,n){return t.is("form")?e(t[0].elements).filter(n):t.find(n)},disableFormElements:function(t){n.formElements(t,n.disableSelector).each(function(){n.disableFormElement(e(this))})},disableFormElement:function(e){var t=e.is("button")?"html":"val";e.data("ujs:enable-with",e[t]());e[t](e.data("disable-with"));e.prop("disabled",true)},enableFormElements:function(t){n.formElements(t,n.enableSelector).each(function(){n.enableFormElement(e(this))})},enableFormElement:function(e){var t=e.is("button")?"html":"val";if(e.data("ujs:enable-with"))e[t](e.data("ujs:enable-with"));e.prop("disabled",false)},allowAction:function(e){var t=e.data("confirm"),r=false,i;if(!t){return true}if(n.fire(e,"confirm")){r=n.confirm(t);i=n.fire(e,"confirm:complete",[r])}return r&&i},blankInputs:function(t,n,r){var i=e(),s,o,u=n||"input,textarea",a=t.find(u);a.each(function(){s=e(this);o=s.is("input[type=checkbox],input[type=radio]")?s.is(":checked"):s.val();if(!o===!r){if(s.is("input[type=radio]")&&a.filter('input[type=radio]:checked[name="'+s.attr("name")+'"]').length){return true}i=i.add(s)}});return i.length?i:false},nonBlankInputs:function(e,t){return n.blankInputs(e,t,true)},stopEverything:function(t){e(t.target).trigger("ujs:everythingStopped");t.stopImmediatePropagation();return false},disableElement:function(e){e.data("ujs:enable-with",e.html());e.html(e.data("disable-with"));e.bind("click.railsDisable",function(e){return n.stopEverything(e)})},enableElement:function(e){if(e.data("ujs:enable-with")!==t){e.html(e.data("ujs:enable-with"));e.removeData("ujs:enable-with")}e.unbind("click.railsDisable")}};if(n.fire(r,"rails:attachBindings")){e.ajaxPrefilter(function(e,t,r){if(!e.crossDomain){n.CSRFProtection(r)}});r.delegate(n.linkDisableSelector,"ajax:complete",function(){n.enableElement(e(this))});r.delegate(n.buttonDisableSelector,"ajax:complete",function(){n.enableFormElement(e(this))});r.delegate(n.linkClickSelector,"click.rails",function(r){var i=e(this),s=i.data("method"),o=i.data("params"),u=r.metaKey||r.ctrlKey;if(!n.allowAction(i))return n.stopEverything(r);if(!u&&i.is(n.linkDisableSelector))n.disableElement(i);if(i.data("remote")!==t){if(u&&(!s||s==="GET")&&!o){return true}var a=n.handleRemote(i);if(a===false){n.enableElement(i)}else{a.error(function(){n.enableElement(i)})}return false}else if(i.data("method")){n.handleMethod(i);return false}});r.delegate(n.buttonClickSelector,"click.rails",function(t){var r=e(this);if(!n.allowAction(r))return n.stopEverything(t);if(r.is(n.buttonDisableSelector))n.disableFormElement(r);var i=n.handleRemote(r);if(i===false){n.enableFormElement(r)}else{i.error(function(){n.enableFormElement(r)})}return false});r.delegate(n.inputChangeSelector,"change.rails",function(t){var r=e(this);if(!n.allowAction(r))return n.stopEverything(t);n.handleRemote(r);return false});r.delegate(n.formSubmitSelector,"submit.rails",function(r){var i=e(this),s=i.data("remote")!==t,o,u;if(!n.allowAction(i))return n.stopEverything(r);if(i.attr("novalidate")==t){o=n.blankInputs(i,n.requiredInputSelector);if(o&&n.fire(i,"ajax:aborted:required",[o])){return n.stopEverything(r)}}if(s){u=n.nonBlankInputs(i,n.fileInputSelector);if(u){setTimeout(function(){n.disableFormElements(i)},13);var a=n.fire(i,"ajax:aborted:file",[u]);if(!a){setTimeout(function(){n.enableFormElements(i)},13)}return a}n.handleRemote(i);return false}else{setTimeout(function(){n.disableFormElements(i)},13)}});r.delegate(n.formInputClickSelector,"click.rails",function(t){var r=e(this);if(!n.allowAction(r))return n.stopEverything(t);var i=r.attr("name"),s=i?{name:i,value:r.val()}:null;r.closest("form").data("ujs:submit-button",s)});r.delegate(n.formSubmitSelector,"ajax:send.rails",function(t){if(this==t.target)n.disableFormElements(e(this))});r.delegate(n.formSubmitSelector,"ajax:complete.rails",function(t){if(this==t.target)n.enableFormElements(e(this))});e(function(){n.refreshCSRFTokens()})}})(jQuery)
    </script>
    <style>
        a.status-1{
            font-weight: bold;
        }
    </style>
    <script>
        jQuery(document).ready(function($){

            $.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    console.log('beforesend');
                    settings.data += "&_token=<?= csrf_token() ?>";
                }
            });

            $('.editable').editable().on('hidden', function(e, reason){
                var locale = $(this).data('locale');
                if(reason === 'save'){
                    $(this).removeClass('status-0').addClass('status-1');
                }
                if(reason === 'save' || reason === 'nochange') {
                    var $next = $(this).closest('tr').next().find('.editable.locale-'+locale);
                    setTimeout(function() {
                        $next.editable('show');
                    }, 300);
                }
            });

            $('.group-select').on('change', function(){
                var group = $(this).val();
                if (group) {
                    window.location.href = '<?= action('\Barryvdh\TranslationManager\Controller@getView') ?>/'+$(this).val();
                } else {
                    window.location.href = '<?= action('\Barryvdh\TranslationManager\Controller@getIndex') ?>';
                }
            });

            $("a.delete-key").click(function(event){

              event.preventDefault();
              var row = $(this).closest('tr');
              var url = $(this).attr('href');
              var id = row.attr('id');
              $.post( url, {id: id}, function(){
                  row.remove();
              } );
            });

            $('.form-import').on('ajax:success', function (e, data) {
                $('div.success-import strong.counter').text(data.counter);
                $('div.success-import').slideDown();
            });

            $('.form-find').on('ajax:success', function (e, data) {
                $('div.success-find strong.counter').text(data.counter);
                $('div.success-find').slideDown();
            });

            $('.form-publish').on('ajax:success', function (e, data) {
                $('div.success-publish').slideDown();
            });

            $('input[type="checkbox"]').click(function(){
                var inputValue = $(this).attr("value");
                //alert("val "+inputValue);
                //$("." + inputValue).toggle();
                myId = "ln-locale-"+inputValue;

                var ckVal = $(this).val();
                //alert(ckVal);
                if($(this).is(":checked")) {
                    //$('[id^=myId]').show();

                    $( "."+ckVal ).each(function() {
                        $( this ).show();
                    });
                } else {
                    //$('[id^=myId]').hide();
                    $( "."+ckVal).each(function() {
                        $( this ).hide();
                    });

                }

            });

        })
    </script>
</head>
<body>
<div style="width: 80%; margin: auto;">
    <h1>Translation Manager</h1>
    <p>Warning, translations are not visible until they are exported back to the app/lang file, using 'php artisan translation:export' command or publish button.</p>
    <div>Use dot to search in group/key, example: sidebar.general</div>
    <div>Use * to search in group/value, example: sidebar*general</div>
    <div class="alert alert-success success-import" style="display:none;">
        <p>Done importing, processed <strong class="counter">N</strong> items! Reload this page to refresh the groups!</p>
    </div>
    <div class="alert alert-success success-find" style="display:none;">
        <p>Done searching for translations, found <strong class="counter">N</strong> items!</p>
    </div>
    <div class="alert alert-success success-publish" style="display:none;">
        <p>Done publishing the translations for '<?= $group!=''?'group '.$group:'all groups' ?>'!</p>
    </div>
    <?php if(Session::has('successPublish')) : ?>
        <div class="alert alert-info">
            <?php echo Session::get('successPublish'); ?>
        </div>
    <?php endif; ?>
    <div class="col-md-3">
        <?php if(!isset($group)) : ?>
        <form class="form-inline form-import" method="POST" action="<?= action('\Barryvdh\TranslationManager\Controller@postImport') ?>" data-remote="true" role="form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <select name="replace" class="form-control">
                <option value="0">Append new translations</option>
                <!--option value="1">Replace existing translations</option-->
            </select>
            <button type="submit" class="btn btn-success"  data-disable-with="Loading..">Import groups</button>
        </form>
        <form class="form-inline form-find" method="POST" action="<?= action('\Barryvdh\TranslationManager\Controller@postFind') ?>" data-remote="true" role="form" data-confirm="Are you sure you want to scan you app folder? All found translation keys will be added to the database.">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <p></p>
            <!--<button-- type="submit" class="btn btn-info" data-disable-with="Searching.." >Find translations in files</button-->
        </form>
        <?php endif; ?>
        <?php if(isset($group)) : ?>
            <form class="form-inline form-publish" method="POST" action="<?= action('\Barryvdh\TranslationManager\Controller@postPublish', ($group!=''?$group:'*'))?>" data-remote="true" role="form" data-confirm="Are you sure you want to publish <?=$group!=''?'the translations group '.$group:' all translations' ?>? This will overwrite existing language files.">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="groupAndKeyArray" value="<php echo serialize($groupAndKeyArray) ?>">
                <button type="submit" class="btn btn-info" data-disable-with="Publishing.." >Publish translations</button>
                <a href="<?= action('\Barryvdh\TranslationManager\Controller@getIndex') ?>" class="btn btn-default">Back</a>
            </form>
        <?php endif; ?>
</div>
    <div class="col-md-3">
        <form name="filter-form" method="POST" action="<?= action('\Barryvdh\TranslationManager\Controller@postFindOnDb') ?>" accept-charset="UTF-8" id="filter-form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="input-group custom-search-form">
                <input type="text" class="form-control" id="search"  name="search" value="<?php if (isset($search)) echo $search?>" placeholder="search">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit" id="search-merchants">
                        <span class="glyphicon glyphicon-search"></span>
                    </button>
                    <?php if (isset($search) && $search != '') {?>
                        <a href="javascript:void(0);" onclick="$('#search').val('')" class="btn btn-danger del-filter" type="button" >
                            <span class="glyphicon glyphicon-remove"></span>
                        </a>
                    <?php } ?>
                </span>
            </div>


                    <?php foreach($locales as $locale):
                        $langChecked = "";
                        if(in_array($locale, $langSelectedArray)) {
                            $langChecked = "checked";
                        }
                        //echo "lan ".$langChecked;
                    ?>
                        <b><?= $locale ?></b> <input type="checkbox" value="<?= $locale ?>" id="lang" name="lang[]" <?= $langChecked?>>&nbsp;&nbsp;&nbsp;&nbsp;

                    <?php endforeach; ?>


        </form>
</div>



    <form role="form">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <div class="form-group">
            <select name="group" id="group" class="form-control group-select">
                <?php foreach($groups as $key => $value): ?>
                    <option value="<?= $key ?>"<?= $key == $group ? ' selected':'' ?>><?= $value ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>



    <?php if((isset($group) && !empty($group)) || (isset($search) && !empty($search)) ) { ?>
    <table class="table">
        <thead>
        <tr>
            <th width="8%">Package</th>
            <th width="8%">Group</th>
            <th width="12%">Key</th>
            <?php
            $languageArray = array();
            $index = 0;
            foreach($locales as $locale):
                if(in_array($locale, $langSelectedArray)) {
                    $languageArray[$index] = $locale;
                    $index++;
                ?>
                <th class="<?=$locale ?>"><?= $locale ?></th>
                <?php
                }
                ?>
            <?php endforeach; ?>
            <?php if($deleteEnabled): ?>
                <th>&nbsp;</th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>

        <?php
        $packagePrev = "";
        $groupPrev = "";
        $keyPrev = "";

        foreach($translations as $index => $translation):
            //var_dump($translations);$group="ciao";
            //echo "sepa ".$separator." index ".$index;exit;return 0;
            list($package,$group,$key,$value) = explode($separator,$index);

            //$myGroup = $group;
            //if (!$group) $myGroup = $groupAndKeyArray[$key];

            $editUrl = action('\Barryvdh\TranslationManager\Controller@postEdit', $group);
            if($package!=''){
                $editUrl = action('\Barryvdh\TranslationManager\Controller@postEdit', $package.'::'.$group);
            }
            //echo "<br>groupPrev: ".$groupPrev." - group ".$group." - keyPrev ".$keyPrev." - key ".$key;
            if ($groupPrev != $group || $keyPrev != $key|| $packagePrev != $package) :
                $packagePrev = $package;
                $groupPrev = $group;
                $keyPrev = $key;
            ?>
            <tr id="<?= $key ?>">
                <td><?= $package ?></td>
                <td><?= $group ?></td>
                <td><?= $key ?></td>
                <?php
                $i = 0;
                foreach($locales as $locale):

                    if(in_array($locale, $langSelectedArray)) { ?>
                        <?php
                        $pos = $package.$separator.$group.$separator.$key.$separator.$locale;
                        //var_dump($translations[$pos]);
                        $t = isset($translations[$pos]) ? $translations[$pos] : null ?>

                        <td class="<?= $languageArray[$i] ?>">
                            <a href="#edit" class="editable status-<?= $t ? $t->status : 0 ?> locale-<?= $locale ?>"
                               data-locale="<?= $locale ?>" data-name="<?= $locale . "|" . $key ?>" id="username"
                               data-type="textarea" data-pk="<?= $t ? $t->id : 0 ?>" data-url="<?= $editUrl ?>"
                               data-title="Enter translation"><?= $t ? htmlentities($t->value, ENT_QUOTES, 'UTF-8', false) : '' ?></a>
                        </td>
                        <?php
                        $i++;
                    }
                endforeach; ?>
                <?php if($deleteEnabled): ?>
                    <td>
                        <a href="<?= action('\Barryvdh\TranslationManager\Controller@postDelete', [$group, $key]) ?>" class="delete-key" data-confirm="Are you sure you want to delete the translations for '<?= $key ?>?"><span class="glyphicon glyphicon-trash"></span></a>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endif; ?>
        <?php endforeach; ?>

        </tbody>
    </table>

    <?php }else{ ?>
    <p>Choose a group to display the group translations. If no groups are visible, make sure you have run the migrations and imported the translations.</p>
    <?php } ?>
</div>

</body>
</html>
