<form class="form-vertical" method="get">
    <input type="hidden" id="esl_lat" name="lat" value="<?php echo $this->get_lat(); ?>" />
    <input type="hidden" id="esl_lng" name="lng" value="<?php echo $this->get_lng(); ?>" />
    <div class="form-group row">
        <label for="esl_address" class="col-sm-2 col-form-label">Address</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="esl_address" name="address" placeholder="Enter your address" value="<?php echo $this->get_address(); ?>" />
        </div>
    </div>
    <div class="form-group row">
        <label for="esl_category" class="col-sm-2 col-form-label">Category</label>
        <div class="col-sm-4">
            <select id="esl_category" name="category" class="form-control">
<?php
$this->print_category_options();
?>
            </select>
        </div>
        <label for="esl_radius" class="col-sm-2 col-form-label">Radius</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" id="esl_radius" name="radius" placeholder="10" value="<?php echo $this->get_radius(); ?>" />
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2">&nbsp;</div>
        <div class="col-sm-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="esl_gps" value="1" />
                <label class="form-check-label" for="gridCheck">My current location</label>
            </div>
        </div>
        <div class="col-sm-5 text-right">
            <input type="submit" class="btn btn-primary" value="Search" />
        </div>
    </div>
</form>

<div id="mapLocator">
</div>

<!-- Modal -->
<div class="modal fade" id="storeModal" tabindex="-1" role="dialog" aria-labelledby="storeModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="store-modal-title">&nbsp;</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <div id="store-modal-thumb"></div>
                    </div>
                    <div class="col-12 col-sm-8">
                        <div id="store-modal-desc"></div>
                        <div id="store-modal-table"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary" id="store-modal-nav">Navigate</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
var stores = <?php echo json_encode($this->get_stores()); ?>;
var centerLat = <?php echo $this->get_lat(); ?>, centerLng = <?php echo $this->get_lng(); ?>;
</script>