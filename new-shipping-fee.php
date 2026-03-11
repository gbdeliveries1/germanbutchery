        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="d-flex justify-content-between flex-wrap">
                <div class="d-flex align-items-end flex-wrap">
                  <div class="mr-md-3 mr-xl-5">
                    <h2>New shipping fee</h2>
                    <p class="mb-md-0">Saving a new shipping fee.</p>
                  </div>
                  <div class="d-flex">
                    <i class="mdi mdi-home text-muted hover-cursor"></i>
                    <p class="text-muted mb-0 hover-cursor">&nbsp;/&nbsp;Dashboard&nbsp;/&nbsp;</p>
                    <p class="text-primary mb-0 hover-cursor">New shipping fee</p>
                  </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-end flex-wrap">
                  <!--<button type="button" class="btn btn-light bg-white btn-icon mr-3 d-none d-md-block ">
                    <i class="mdi mdi-download text-muted"></i>
                  </button>
                  <button type="button" class="btn btn-light bg-white btn-icon mr-3 mt-2 mt-xl-0">
                    <i class="mdi mdi-clock-outline text-muted"></i>
                  </button>
                  <button type="button" class="btn btn-light bg-white btn-icon mr-3 mt-2 mt-xl-0">
                    <i class="mdi mdi-plus text-muted"></i>
                  </button> -->
                  <a href="index.php?shipping-fees"><button class="btn btn-primary mt-2 mt-xl-0"><i class="mdi mdi-clock-outline"></i> View shipping fees</button></a>
                </div>

              </div>
            </div>
          </div>

          <!--
          <div class="row">
            <div class="col-md-7 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title">Cash deposits</p>
                  <p class="mb-4">To start a blog, think of a topic about and first brainstorm party is ways to write details</p>
                  <div id="cash-deposits-chart-legend" class="d-flex justify-content-center pt-3"></div>
                  <canvas id="cash-deposits-chart"></canvas>
                </div>
              </div>
            </div>
            <div class="col-md-5 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title">Total sales</p>
                  <h1>$ 28835</h1>
                  <h4>Gross sales over the years</h4>
                  <p class="text-muted">Today, many people rely on computers to do homework, work, and create or store useful information. Therefore, it is important </p>
                  <div id="total-sales-chart-legend"></div>                  
                </div>
                <canvas id="total-sales-chart"></canvas>
              </div>
            </div>
          </div>
        -->
        <div class="row">
          <div class="col-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Shipping fee information</h4>
                <p class="card-description">
                  Fill in the fields
                </p>
                <form class="forms-sample" method="post" action="#/" onsubmit="new_shipping_fee();return false;">
                  <div class="form-group">
                    <label>Province</label>
                    <select class="form-control" id="province" required>
                      <option value="">Select the province</option>
                      <?php
                      $sql = "SELECT province from rw_location group by province";
                      $result = $conn->query($sql);
                      while ($row = $result->fetch_assoc()) {
                        $province = $row['province'];
                        ?>
                        <option value="<?php echo $province; ?>"><?php echo $province; ?></option>
                        <?php 
                      } ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Shipping fee</label>
                    <input type="number" class="form-control" id="fee" placeholder="Shipping fee" required>
                  </div>
                  <input type="text" id="result_response" style="display: none;">
                  <button type="submit" class="btn btn-primary mr-2">Submit</button>
                  <button class="btn btn-light">Cancel</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>