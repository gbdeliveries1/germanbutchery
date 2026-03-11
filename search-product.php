<?php

if (isset($_POST['search_text'])) {
    $search = $_POST['search_text'];
} else {
    $search = '';
}

?>        
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="d-flex justify-content-between flex-wrap">
                <div class="d-flex align-items-end flex-wrap">
                  <div class="mr-md-3 mr-xl-5">
                    <h2>Products</h2>
                    <p class="mb-md-0">All registered.</p>
                  </div>
                  <div class="d-flex">
                    <i class="mdi mdi-home text-muted hover-cursor"></i>
                    <p class="text-muted mb-0 hover-cursor">&nbsp;/&nbsp;Dashboard&nbsp;/&nbsp;</p>
                    <p class="text-primary mb-0 hover-cursor">Products</p>
                  </div>
                </div>
                
                <!--<div class="d-flex justify-content-between align-items-end flex-wrap">
                  <button type="button" class="btn btn-light bg-white btn-icon mr-3 d-none d-md-block ">
                    <i class="mdi mdi-download text-muted"></i>
                  </button>
                  <button type="button" class="btn btn-light bg-white btn-icon mr-3 mt-2 mt-xl-0">
                    <i class="mdi mdi-clock-outline text-muted"></i>
                  </button>
                  <button type="button" class="btn btn-light bg-white btn-icon mr-3 mt-2 mt-xl-0">
                    <i class="mdi mdi-plus text-muted"></i>
                  </button> 
                  <a href="index.php?new-product"><button class="btn btn-primary mt-2 mt-xl-0"><i class="mdi mdi-plus"></i> New product</button></a>
                </div>-->

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
                <p class="card-title">Products</p>

<form action="index.php?search-product" method="post">
  <input class="form-control" type="text" id="search_text" placeholder='Search results for "<?php echo $search; ?>"' name="search_text" style="border:2px solid gray;"><br>
  <input class="form-control" type="submit" value="Search" style="background:#4C82FF;color:white;">
</form>

                <nav class="product-filter">

                  <h3>All products</h3>

                  <!--<div class="sort">

                    <div class="collection-sort">
                      <label>Filter by:</label>
                      <select>
                        <option value="ALL">All products</option>
                        <?php
                        $sql = "SELECT category_id,count(*) from product group by category_id";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                          $category_id = $row['category_id'];

                          $sql2 = "SELECT * from product_category where category_id='$category_id'";
                          $result2 = $conn->query($sql2);
                          while ($row2 = $result2->fetch_assoc()) {
                            $category_name = $row2['category_name'];
                          }

                          ?>
                          <option value="<?php echo $category_id; ?>"><?php echo $category_name; ?></option>
                          <?php 
                        } ?>
                      </select>
                    </div>

                    <div class="collection-sort">
                      <label>Sort by:</label>
                      <select>
                        <option value="NEW">New</option>
                        <option value="OLD">Old</option>
                      </select>
                    </div>

                  </div>-->

                </nav>

                <section class="products">

                  <?php

                  $sql53="SELECT * from product where product_name like '%".$search."%' order by register_date DESC";
                  $result53=$conn->query($sql53);
                  $a = 1;
                  while ($row53 = $result53->fetch_assoc()) {
                    $product_id =$row53['product_id'];
                    $category_id=$row53['category_id'];
                  	$seller_id=$row53['seller_id'];
                    $sub_category_id =$row53['sub_category_id'];
                    $product_name =$row53['product_name'];
                    $product_unit =$row53['product_unit'];
                    $short_description =$row53['short_description'];
                    $long_description =$row53['long_description'];
                    $register_date =$row53['register_date'];

                    $sql="SELECT * from product_stock where product_id='$product_id'";
                    $result=$conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                      $product_quantity=$row['stock_quantity'];
                    }

                    $sql="SELECT * from product_price where product_id='$product_id'";
                    $result=$conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                      $product_price=$row['price'];
                    }
                  
            $sql2="SELECT * from user where user_id='$seller_id'";
    		$result2=$conn->query($sql2);
    		while ($row2 = $result2->fetch_assoc()) {
            	$first_name = $row2['first_name'];
        		$last_name=$row2['last_name'];
    		}

                    $id1="Mine".$a;
                    $id2="Mine1".$a;
                    $id3="Mine2".$a;
                    $id4="Mine3".$a;

                    ?>  

                    <div class="product-card">
                      <div class="product-image">
                        <div id="carousel<?php echo $a; ?>Indicators" class="carousel slide" data-ride="carousel">
                          <ol class="carousel-indicators">
                            <?php
                            $sql="SELECT * from product_picture where product_id='$product_id' order by register_date DESC limit 1";
                            $result=$conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                              $picture=$row['picture'];

                              ?>
                              <li data-target="#carousel<?php echo $a; ?>Indicators" data-slide-to="0" class="active"></li>
                              <?php

                            }

                            $sql="SELECT * from product_picture where product_id='$product_id' and picture not like '$picture' order by register_date DESC";
                            $result=$conn->query($sql);
                            $cc = 1;
                            while ($row = $result->fetch_assoc()) {
                              $picture=$row['picture'];

                              ?>
                              <li data-target="#carousel<?php echo $a; ?>Indicators" data-slide-to="<?php echo $cc; ?>"></li>
                              <?php
                              
                              $cc++;}
                              ?>

                            </ol>
                            <div class="carousel-inner">

                              <?php
                              $sql="SELECT * from product_picture where product_id='$product_id' order by register_date DESC limit 1";
                              $result=$conn->query($sql);
                              while ($row = $result->fetch_assoc()) {
                                $picture=$row['picture'];

                                ?>
                                <div class="carousel-item active">
                                  <img class="d-block w-100" src="../../uploads/<?php echo $picture; ?>" alt="<?php echo $product_name; ?>">
                                </div>
                                <?php

                              }

                              $sql="SELECT * from product_picture where product_id='$product_id' and picture not like '$picture' order by register_date DESC";
                              $result=$conn->query($sql);
                              while ($row = $result->fetch_assoc()) {
                                $picture=$row['picture'];

                                ?>
                                <div class="carousel-item">
                                  <img class="d-block w-100" src="../../uploads/<?php echo $picture; ?>" alt="<?php echo $product_name; ?>">
                                </div>
                                <?php

                              }
                              ?>

                              
                              
                            </div>
                            <a class="carousel-control-prev" href="#carousel<?php echo $a; ?>Indicators" role="button" data-slide="prev">
                              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </a>
                            <a class="carousel-control-next" href="#carousel<?php echo $a; ?>Indicators" role="button" data-slide="next">
                              <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </a>
                          </div>
                          <div class="product-info">
                            <h5><?php echo $product_name; ?></h5>
                            <hr>
                            <h6><?php echo $product_price; ?> RWF / <?php echo $product_unit; ?></h6>
                          	<hr>
                            <h6><?php echo $first_name." ".$last_name; ?></h6>
                          	<hr>
                          	<a href='#/' onclick="delete_product('<?php echo $product_id; ?>')"><button class="btn btn-sm btn-danger mr-2">Delete</button></a>
                          </div>
                        </div>
                      </div>

                      <?php $a++; } ?>
                
                	<input type="text" id="result_response" style="display: none;">

                    </section>

                  </div>
                </div>
              </div>
            </div>
          </div>