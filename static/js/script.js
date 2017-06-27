$(document).ready(function(){

  $('.down').parent().next().next().slideToggle(0);

  $('.down').click(function(){
    $(this).parent().next().next().slideToggle(300);
  });

});

function showInfo(id, item){
  if($('#' + id).hasClass('showing')){

  }
  else {
    $('.active').removeClass('active');
    $('.showing').removeClass('showing');
    $('main').hide()
    $('#' + id).slideDown(300);
    $('#' + id).addClass('showing');
    $(item).addClass('active');
  }
}

function setTipps(target){
  var match_id = $(target).attr('data-match');
  var tHome = $('.home' + match_id).val();
  var tAway = $('.away' + match_id).val();
  console.log("MatchId - " + match_id + " tHome - " + tHome + " tAway - " + tAway + " UserId - " + user_id);
  if(tHome != null && tAway != null) {
    var data = {
      'match_id': match_id,
      'tHome': tHome,
      'tAway': tAway,
      'user_id': user_id
    }
    var req = "https://em.nh-os.de/include/process_tipp.php";
    $.post(req, data, function(rData, status){

    }).done(function(rData){
      console.log(rData);
      var data = rData.split('##');
      if(data[1] == 'success'){
        $(target).parent().children('.marked').removeClass('hide');
        $(target).html('Ändern');
        $(target).fadeOut(300);
        $(target).fadeIn(300);
      }
    });
  }
}

function setResult(target){
  var match_id = $(target).attr('data-match');
  var tHome = $('.home' + match_id).val();
  var tAway = $('.away' + match_id).val();
  console.log("MatchId - " + match_id + " tHome - " + tHome + " tAway - " + tAway + " UserId - " + user_id);
  if(tHome != null && tAway != null) {
    var data = {
      'match_id': match_id,
      'rHome': tHome,
      'rAway': tAway,
      'user_id': user_id
    }
    var req = "https://em.nh-os.de/include/process_result.php";
    $.post(req, data, function(rData, status){

    }).done(function(rData){
      console.log(rData);
      var data = rData.split('##');
      if(data[1] == 'success'){
        $(target).parent().hide();
      }
    });
  }
}
