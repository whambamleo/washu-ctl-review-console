<?php
/*
Template Name: Single Response
*/
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Parse the current URL
        const urlParams = new URLSearchParams(window.location.search);

        // Get the responseId parameter
        const responseId = urlParams.get('responseId');

        // Log it to the console
        console.log(responseId);

        // Now you can use responseId to make your GET request
        // ...
    });
</script>


