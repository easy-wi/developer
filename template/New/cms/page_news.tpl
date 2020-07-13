<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $page_data->pages['news']['linkname'];?></li>
        </ul>
    </div>
</div>

<?php foreach($news as $single) { ?>
    <div class="jumbotron">
      <h1 class="display-4"><?php echo $single['href'];?></h1>
    <p class="lead"><?php echo $single['text'];?></p>
          <hr class="my-4">
           <p> <?php echo $page_sprache->categories.': '.implode(', ',returnPlainArray($single['categories'],'href'));?>
  <br>
            <?php echo $page_sprache->tag.': '.implode(', ',returnPlainArray($single['tags'],'href'));?></p></div>

<?php } ?>

