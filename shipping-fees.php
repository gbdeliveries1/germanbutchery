        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="d-flex justify-content-between flex-wrap">
                <div class="d-flex align-items-end flex-wrap">
                  <div class="mr-md-3 mr-xl-5">
                    <h2>Shipping fees</h2>
                    <p class="mb-md-0">All registered.</p>
                  </div>
                  <div class="d-flex">
                    <i class="mdi mdi-home text-muted hover-cursor"></i>
                    <p class="text-muted mb-0 hover-cursor">&nbsp;/&nbsp;Dashboard&nbsp;/&nbsp;</p>
                    <p class="text-primary mb-0 hover-cursor">Shipping fees</p>
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
                  <a href="index.php?new-shipping-fee"><button class="btn btn-primary mt-2 mt-xl-0"><i class="mdi mdi-plus"></i> New shipping fee</button></a>
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
          <div class="col-md-12 stretch-card">
            <div class="card">
              <div class="card-body">
                <p class="card-title">Shipping fees</p>
                <div class="table-responsive">
                  <table id="recent-purchases-listing" class="table table_1">
                    <thead>
                      <tr>
                        <th style="display: none;"></th>
                        <th>Province</th>
                        <th>Shipping fee</th>
                      	<th>ACTION</th>
                      </tr>
                    </thead>
                    <tbody>

                      <?php

                      $sql53="SELECT * from shipping_fee";
                      $result53=$conn->query($sql53);
                      $a = 1;
                      while ($row53 = $result53->fetch_assoc()) {
                        $fee_id =$row53['fee_id'];
                        $province=$row53['province'];
                        $fee =$row53['fee'];

                        $id1="Mine".$a;
                        $id2="Mine1".$a;
                        $id3="Mine2".$a;
                        $id4="Mine3".$a;

                        ?>   

                        <tr id='<?php echo $id3; ?>'>
                          <td id="<?php echo $id1; ?>" style='display: none;'><?php echo $fee_id ; ?></td>
                          <td><?php echo $province; ?></td>
                          <td><?php echo $fee; ?></td>
                          <td><button class='btn btn-sm btn-warning' onclick="delete_shipping_fee('<?php echo $fee_id ?>');">DELETE FEE</button></td>
                        </tr>

                        <?php $a++; } ?>

                      </tbody>
                    </table>
                	<input type="text" id="result_response" style="display: none;">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>