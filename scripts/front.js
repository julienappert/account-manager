function acma_yes_deleteGalleryItem(that){
  var $item = jQuery(that).parents('li');
  var $gallery = jQuery(that).parents('.form-gallery');
  var $field = $gallery.find('.gallery_ids');

  $item.toggle(400, function(){
    jQuery(this).remove();
    $itemid = $item.attr('id');
    var $ids = $field.val().split(',');
    var $newids = [];
    for(i in $ids){
      if($ids[i] != $itemid){
        $newids.push($ids[i]);
      }
    }
    $field.val($newids);
  });
}

jQuery(function($) {

  // pour les formulaires de acma
  $('form.acma-form').each(function(){

    $('.form-group.has-error').each(function(){
      var that = $(this);
      $(this).find('input').blur(function(){
        if($(this).get(0).checkValidity()){
          that.removeClass('has-error');
          that.find('.help-block').hide();
        }
        else{
          that.addClass('has-error');
          that.find('.help-block').show();
        }
      });
    });

    $('.form-gallery').each(function(){

      var $gallery = $(this);
      var $photos_thumb =  $gallery.find( ".photos-thumbnail ul" );
      var $field = $gallery.find('.gallery_ids');

      $photos_thumb.sortable({
        update:function(event, ui){
          var photos = '';
          $photos_thumb.find( "li" ).each(function(){
            if(photos.length == 0) photos += $(this).attr('id');
             else photos += ',' + $(this).attr('id');
          });
          $field.val(photos);
        }
      });

      var uploader = new plupload.Uploader({
        browse_button: $gallery.find('.browse').get(0), // this can be an id of a DOM element or the DOM element itself
        url: ajaxurl,
        'multipart_params':{
          '_ajax_nonce': $gallery.find('.acma_ajax_nonce').val(), // will be added per uploader
          'action': 'acma_ajax_action', // the ajax action name
          'postid':  $gallery.find('.gallery_postid').val() // will be added per uploader
        },
        max_file_size:'2mb',
        filters:[
          { title: 'Fichiers Image', extensions: 'jpg,gif,png'}
        ]
      });

      uploader.init();
      uploader.bind('Browse', function(up, files) {
          $gallery.find('.console').html('').hide();
      });


      uploader.bind('FilesAdded', function(up, files) {
        var html = '';
        plupload.each(files, function(file) {
          html += '<li id="' + file.id + '"><i class="fa fa-download" aria-hidden="true"></i> ' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></li>';
        });
        $gallery.find('.filelist').append(html);

      });

      uploader.bind('UploadProgress', function(up, file) {
        document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
      });

      uploader.bind('Error', function(up, err) {
        console.log(err);
        if(err.code == '-600'){
          var message = '<div>Fichier ' + err.file.name + ' trop volumineux.</div>';
        }
        else{
          var message = "<div>Error #" + err.code + ": " + err.message + '</div>';
        }
        $gallery.find('.console').append(message).show();
      });

      uploader.bind('FileUploaded', function(up, file, result) {
        result = jQuery.parseJSON(result.response);
        console.log(file); console.log(result);
        var image = $('<li>', {
          'id': result.id,
          'class': 'photo-thumbnail',
          'html': '<button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-trash"></i></button><div class="img" style="background-image:url('+ result.url +')"></div>'
        });
        $photos_thumb.append(image);
        acma_deleteGalleryItem(image);
        $gallery.find('#' + file.id).remove();
        var photos = $field.val();
        if(photos.length > 0) $field.val( photos + ',' + result.id );
        else $field.val( result.id );
      });

      $gallery.find('.start-upload').click(function() {
        uploader.start();
      });

      $photos_thumb.find('li').each(function(){
        acma_deleteGalleryItem($(this));
      });

      function acma_deleteGalleryItem(elem){
        elem.find('button').popover({
          'title':  "Supprimer la photo",
          'html': true,
          'placement': 'bottom',
          'content': '<p>Merci de confirmer la suppression</p><p><button type="button" class="btn btn-danger btn-block" onclick="acma_yes_deleteGalleryItem(this)">Oui, supprimer</button></p>'
        });
      }

    });

    $('.form-date').each(function(){
      var args = {};
      if($(this)[0].hasAttribute("data-lang")){
        args.language = $(this).attr('data-lang');
      }
      if($(this)[0].hasAttribute("data-format")){
        args.format = $(this).attr('data-format');
      }
      if($(this)[0].hasAttribute("data-daysOfWeekDisabled")){
        args.daysOfWeekDisabled = $(this).attr('data-daysOfWeekDisabled');
      }
      if($(this)[0].hasAttribute("data-datesDisabled")){
        args.datesDisabled = $(this).attr('data-datesDisabled');
      }
      $(this).datepicker(args);
    });


    $('.form-time').each(function(){
      var args = {};
      if($(this)[0].hasAttribute("data-step")){
        args.step = $(this).attr('data-step');
      }
      if($(this)[0].hasAttribute("data-timeFormat")){
        args.timeFormat = $(this).attr('data-timeFormat');
      }
      if($(this)[0].hasAttribute("data-minTime")){
        args.minTime = $(this).attr('data-minTime');
      }
      if($(this)[0].hasAttribute("data-maxTime")){
        args.maxTime = $(this).attr('data-maxTime');
      }
      $(this).timepicker(args);
    });


    $('.password-container').each(function(){
      var passwd_container = $(this);

      //active l'affichage en clair du mot de passe
      passwd_container.find('.showpassword').click(function(){
        var inputpass = passwd_container.find('input[name=password], input[name=oldpassword]');
        if($(this).hasClass('shown')){
          inputpass.attr('type','password');
          $(this).removeClass('shown');
          $(this).find('i.fa').removeClass('fa-eye-slash').addClass('fa-eye');
          $(this).attr('title', acma.register.show_password);
        }
        else{
          inputpass.attr('type','text');
          $(this).addClass('shown');
          $(this).find('i.fa').removeClass('fa-eye').addClass('fa-eye-slash');
          $(this).attr('title', acma.register.hide_password);
        }
      });


      // affiche le score de complexité du mot de passe
      passwd_container.find('input[name=password]').keyup(function(){
        var passwd_score = passwd_container.find('.password-score');

        if($(this).val().length == 0){
          passwd_score.text(' ');
          passwd_container.removeClass('score_0 score_1 score_2 score_3 score_4');
          return false;
        }
        var result = zxcvbn($(this).val());
        passwd_container.removeClass('score_0 score_1 score_2 score_3 score_4');
        switch(result.score){
          case 0:
            passwd_score.text(acma.register.password_tooweak);
            passwd_container.addClass('score_0');
          break;
          case 1:
            passwd_score.text(acma.register.password_weak);
            passwd_container.addClass('score_1');
          break;
          case 2:
            passwd_score.text(acma.register.password_somewhat);
            passwd_container.addClass('score_2');
          break;
          case 3:
            passwd_score.text(acma.register.password_safe);
            passwd_container.addClass('score_3');
          break;
          case 4:
            passwd_score.text(acma.register.password_verysafe);
            passwd_container.addClass('score_4');
          break;
        }
      });

    });

  });
});
