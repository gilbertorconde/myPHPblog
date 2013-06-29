<?php

echo '
<!--<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script> -->
<script src="tinyMCE/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea",
    //language_url: "tinyMCE/langs/pt_BR.js",
    language: \'pt_BR\',
    plugins: [
		"advlist autolink lists link image charmap print preview anchor",
		"searchreplace visualblocks code fullscreen",
		"insertdatetime media table contextmenu paste"
	     ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
    autosave_ask_before_unload: false,
    width: 630,
    max_height: 400,
    min_height: 160,
    height : 350
 });
</script>
';

?>