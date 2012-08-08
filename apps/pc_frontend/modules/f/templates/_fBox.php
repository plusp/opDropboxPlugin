<div class="dropslot"></div>

<script id="fileListTemplate" type="text/x-jquery-tmpl">
<ul class="nav nav-tabs nav-stacked">
<li class="active"><a href="#">${title}</a></li>
 {{each data}}
<li><a class="shareLink" href="/f?path={{html encodeURIComponent(name)}}">${original_filename}</a></li>
{{/each}}
</ul>
</script>

<script>
  var request = {};
  request.apiKey = openpne.apiKey;
  request.path = '/m<?php echo $member->getId(); ?>';
  var $pushHtml;
  $.get(openpne.apiBase + 'f/list',request,function(json){
    console.log(json);
    json.title = "マイファイルリスト";
    $pushHtml = $("#fileListTemplate").tmpl(json);
    $(".dropslot").append($pushHtml);
  });
  
</script>

<form class="well" action="/api.php/f/upload" method="post" enctype="multipart/form-data" target="upload_f2">
  アップロード：<br />
  <input class="fileupl2" type="hidden" name="apiKey" value="" />
  <input type="file" name="upfile" size="5" /><br />
  <br />
  <input class="btn" type="submit" value="送信" />
<br />
</form>

    <div id="container"></div>
    <iframe name="upload_f2" style="display:none;"></iframe>


<script>
$("input.fileupl2").attr("value",openpne.apiKey);
</script>
