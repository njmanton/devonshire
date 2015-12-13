<!-- app/View/elements/finalise.ctp -->
<form action="/predictions/complete/<?=$week; ?>" name="finaliseform" id="finaliseform" style="display: none;" method="post">
</form>
<button class="alert alert-box info" type="button" onclick="if (confirm('Are you sure?')) { document.finaliseform.submit(); } event.returnValue = false; return false;">Click to finalise week</button>

