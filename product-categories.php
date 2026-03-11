        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="d-flex justify-content-between flex-wrap">
                <div class="d-flex align-items-end flex-wrap">
                  <div class="mr-md-3 mr-xl-5">
                    <h2>Product categories</h2>
                    <p class="mb-md-0">All registered.</p>
                  </div>
                  <div class="d-flex">
                    <i class="mdi mdi-home text-muted hover-cursor"></i>
                    <p class="text-muted mb-0 hover-cursor">&nbsp;/&nbsp;Dashboard&nbsp;/&nbsp;</p>
                    <p class="text-primary mb-0 hover-cursor">Product categories</p>
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
                  <a href="index.php?new-product-category"><button class="btn btn-primary mt-2 mt-xl-0"><i class="mdi mdi-plus"></i> New category</button></a>
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
                <p class="card-title">Product categories</p>
                <div class="table-responsive">
                  <table id="recent-purchases-listing" class="table table_1">
                    <thead>
                      <tr>
                        <th style="display: none;"></th>
                        <th>Category Name</th>
                      	<th>ACTION</th>
                      </tr>
                    </thead>
                    <tbody>

                      <?php

                      $sql53="SELECT * from product_category";
                      $result53=$conn->query($sql53);
                      $a = 1;
                      while ($row53 = $result53->fetch_assoc()) {
                        $category_id =$row53['category_id'];
                        $category_name=$row53['category_name'];

                        $id1="Mine".$a;
                        $id2="Mine1".$a;
                        $id3="Mine2".$a;
                        $id4="Mine3".$a;

                        ?>   

                        <tr id='<?php echo $id3; ?>'>
                          <td id="<?php echo $id1; ?>" style='display: none;'><?php echo $category_id ; ?></td>
                          <td><?php echo $category_name; ?></td>
                          <td><button class='btn btn-sm btn-warning' onclick="delete_category('<?php echo $category_id ?>');">DELETE CATEGORY</button></td>
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