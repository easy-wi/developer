<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"><?php echo $ewCfg['title'] . ' ' . $serverip .':' . $port;?></h4>
    </div>
    <div class="modal-body">
        <?php echo $log;?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
    </div>
</div>
<script type='text/javascript'>
    $( ".modal-body" ).ready(function() {
        var modalContent = $('.modal-body');
        modalContent.css('overflow-y', 'auto');
        modalContent.css('max-height', $(window).height() * 0.7);
    });
</script>