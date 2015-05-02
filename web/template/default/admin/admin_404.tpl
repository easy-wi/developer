<section class="content-header">
    <h1>
        404 Error Page
    </h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">404 error</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <div class="error-page">
        <h2 class="headline text-yellow"> 404</h2>
        <div class="error-content">
            <h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found.</h3>
            <p>
                We could not find the page you were looking for.
                Meanwhile, you may <a href="admin.php">return to dashboard</a> or try using the search form.
            </p>

            <form action="admin.php" method="get" class="search-form">
                <input type="hidden" name="w" value="sr">
                <?php if($pa['gserver']){ ?><input type="hidden" name="type[]" value="gs"><?php }?>
                <?php if($pa['gimages']){ ?><input type="hidden" name="type[]" value="im"><?php }?>
                <?php if($pa['addons']){ ?><input type="hidden" name="type[]" value="ad"><?php }?>
                <?php if($pa['voiceserver']){ ?><input type="hidden" name="type[]" value="vo"><?php }?>
                <?php if($pa['addvserver'] or $pa['modvserver'] or $pa['delvserver'] or $pa['usevserver']){ ?><input type="hidden" name="type[]" value="vs"><?php }?>
                <?php if($pa['roots']){ ?><input type="hidden" name="type[]" value="ro"><?php }?>
                <?php if($pa['user'] or $pa['user_users']){ ?><input type="hidden" name="type[]" value="us"><?php }?>
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search...">
                    <span class="input-group-btn">
                        <button type='submit' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                    </span>
                </div>
            </form>
        </div><!-- /.error-content -->
    </div><!-- /.error-page -->
</section><!-- /.content -->