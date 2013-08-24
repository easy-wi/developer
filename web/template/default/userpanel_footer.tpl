            </div><!--/span-->
        </div><!--/row-->
        <hr>
        <footer>
            <p>&copy; Easy-WI 2013 - <?php echo date('Y'); ?></p>
        </footer>
    </div><!--/.fluid-container-->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php if (isset($table['tools'])) { foreach ($table['tools'] as $table_row) { echo '<script>$("#'.$table_row['adid'].'-'.$table['id'].'").tooltip();</script>';}}?>
    <?php if (isset($table['maps'])) { foreach ($table['maps'] as $table_row) { echo '<script>$("#'.$table_row['adid'].'-'.$table['id'].'").tooltip();</script>';}}?>
    <?php if (isset($initalize)) { foreach ($initalize as $i) { echo '<script>$("#'.$i.'").tooltip();</script>';}}?>
</body>
</html>