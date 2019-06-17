<?php
$counter = rand();
$filter = $this->getFilter();
$show = $filter->message('spoiler') . ' (' . $filter->message('show') . ')';
$hide = $filter->message('spoiler') . ' (' . $filter->message('hide') . ')'; ?>

<script type="text/javascript">
  if (typeof decodaToggleSpoiler !== 'function') {
    function decodaToggleSpoiler(button, id) {
      var details = document.getElementById('spoiler-content-' + id);

      if (details.style.display === 'none') {
        details.style.display = '';
        button.innerHTML = button.getAttribute('data-text-hide');
      } else {
        details.style.display = 'none';
        button.innerHTML = button.getAttribute('data-text-show');
      }
    }
  }
</script>

<div class="decoda-spoiler">
    <button
        type="button"
        class="decoda-spoiler-button"
        onclick="decodaToggleSpoiler(this, <?php echo $counter; ?>);"
        data-text-show="<?php echo $show; ?>"
        data-text-hide="<?php echo $hide; ?>"
    >
        <?php echo $show; ?>
    </button>

    <div class="decoda-spoiler-content" id="spoiler-content-<?php echo $counter; ?>" style="display: none">
        <?php echo $content; ?>
    </div>
</div>
