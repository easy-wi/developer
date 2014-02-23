                </div><!--/span-->
            </div><!--/row-->
            <hr>
            <footer>
                <p>&copy; Easy-WI 2011 - <?php echo date('Y'); ?></p>
            </footer>
        </div><!--/.fluid-container-->
        <!-- Placed at the end of the document so the pages load faster -->
        <?php if (isset($initalize)) { foreach ($initalize as $i) { echo '<script>$("#'.$i.'").tooltip();</script>';}}?>
        </body>
</html>