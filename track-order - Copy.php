<?php
if (isset($_GET['order_number']) && isset($_GET['phone_number'])) {
    $order_number = $_GET['order_number'];
    $phone_number = $_GET['phone_number'];
} else {
    $order_number = 1;
    $phone_number = 1;
}
?>
            <!--====== Section 1 ======-->
            <div class="u-s-p-y-60">

                <!--====== Section Content ======-->
                <div class="section__content">
                    <div class="container">
                        <div class="breadcrumb">
                            <div class="breadcrumb__wrap">
                                <ul class="breadcrumb__list">
                                    <li class="has-separator">

                                        <a href="index.php">Home</a></li>
                                    <li class="is-marked">

                                        <a href="index.php?track-order&order_number=<?php echo $order_number; ?>&phone_number=<?php echo $phone_number; ?>">Track order</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--====== End - Section 1 ======-->

            <!--====== Section 2 ======-->
            <div class="u-s-p-b-60">

                <!--====== Section Content ======-->
                <div class="section__content">
                    <div class="dash">
                        <div class="container">
                            <div class="row">
                                <!--<div class="col-lg-3 col-md-12">

                                    <div class="dash__box dash__box--bg-white dash__box--shadow u-s-m-b-30">
                                        <div class="dash__pad-1">

                                            <span class="dash__text u-s-m-b-16">Hello, John Doe</span>
                                            <ul class="dash__f-list">
                                                <li>

                                                    <a class="dash-active" href="dashboard.html">Manage My Account</a></li>
                                                <li>

                                                    <a href="dash-my-profile.html">My Profile</a></li>
                                                <li>

                                                    <a href="dash-address-book.html">Address Book</a></li>
                                                <li>

                                                    <a href="dash-track-order.html">Track Order</a></li>
                                                <li>

                                                    <a href="dash-my-order.html">My Orders</a></li>
                                                <li>

                                                    <a href="dash-payment-option.html">My Payment Options</a></li>
                                                <li>

                                                    <a href="dash-cancellation.html">My Returns & Cancellations</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="dash__box dash__box--bg-white dash__box--shadow dash__box--w">
                                        <div class="dash__pad-1">
                                            <ul class="dash__w-list">
                                                <li>
                                                    <div class="dash__w-wrap">

                                                        <span class="dash__w-icon dash__w-icon-style-1"><i class="fas fa-cart-arrow-down"></i></span>

                                                        <span class="dash__w-text">4</span>

                                                        <span class="dash__w-name">Orders Placed</span></div>
                                                </li>
                                                <li>
                                                    <div class="dash__w-wrap">

                                                        <span class="dash__w-icon dash__w-icon-style-2"><i class="fas fa-times"></i></span>

                                                        <span class="dash__w-text">0</span>

                                                        <span class="dash__w-name">Cancel Orders</span></div>
                                                </li>
                                                <li>
                                                    <div class="dash__w-wrap">

                                                        <span class="dash__w-icon dash__w-icon-style-3"><i class="far fa-heart"></i></span>

                                                        <span class="dash__w-text">0</span>

                                                        <span class="dash__w-name">Wishlist</span></div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>-->

<?php

            //$sql12="SELECT * from orders where order_number='$order_number' and phone_no='$phone_number' and status not like 'Canceled'";
            $sql12="SELECT * from orders where order_number='$order_number' and phone_no='$phone_number'";
            $result12=$conn->query($sql12);
            $a = 1;
            if ($row12 = $result12->fetch_assoc()) {

                $order_id=$row12['order_id'];
                $first_name=$row12['first_name'];
                $last_name=$row12['last_name'];
                $email=$row12['email'];
                $phone_no=$row12['phone_no'];
                $order_number=$row12['order_number'];
                $cart_id=$row12['cart_id'];
                $address_id=$row12['address_id'];
                $shipping_amount=$row12['shipping_amount'];
                $sub_total_amount=$row12['sub_total_amount'];
                $total_amount=$row12['total_amount'];
                $order_description=$row12['order_description'];
                $order_date=$row12['order_date'];
                $estimated_delivery_date=$row12['estimated_delivery_date'];
                $shipped_date=$row12['shipped_date'];
                $delivered_date=$row12['delivered_date'];
                $order_status=$row12['status'];

            

            $sql12="SELECT * from address where address_id='$address_id'";
            $result12=$conn->query($sql12);
            $a = 1;
            while ($row12 = $result12->fetch_assoc()) {

                $address_id=$row12['address_id'];
                $user_id=$row12['user_id'];
                $address_type=$row12['address_type'];
                $country=$row12['country'];
                $province=$row12['province'];
                $district=$row12['district'];
                $sector=$row12['sector'];
                $cell=$row12['cell'];
                $village=$row12['village'];
                $street=$row12['street'];
                $described_address=$row12['described_address'];
                $address_register_date=$row12['register_date'];
               
            }

            $sql="SELECT count(*) as p_count from cart_item where cart_id='$cart_id' and order_number='$order_number'";
            $result=$conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $p_count=$row['p_count'];
            }

            $sql="SELECT * from payment where order_id='$order_id'";
            $result=$conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $payment_id=$row['payment_id'];
                $method_id=$row['method_id'];
            }

            $sql="SELECT * from payment_method where method_id='$method_id'";
            $result=$conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $method_name=$row['method_name'];
            }

            $sql12="SELECT * from cart_item where cart_id='$cart_id' and order_number='$order_number' order by register_date DESC";
            $result12=$conn->query($sql12);
            $a = 1;
            while ($row12 = $result12->fetch_assoc()) {
                $item_id=$row12['item_id'];
                $product_id=$row12['product_id'];
                $cart_item_product_quantity=$row12['product_quantity'];
                $cart_item_price=$row12['price'];
                $cart_item_register_date=$row12['register_date'];

                $id1="Mine".$a;
                $id2="Mine1".$a;
                $id3="Mine2".$a;
                $id4="Mine3".$a;

                $sql53="SELECT * from product where product_id='$product_id'";
                $result53=$conn->query($sql53);
                while ($row53 = $result53->fetch_assoc()) {
                    $product_id =$row53['product_id'];
                    $category_id=$row53['category_id'];
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

                    $sql="SELECT * from product_category where category_id='$category_id'";
                    $result=$conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        $category_name=$row['category_name'];
                    }

                }

        $a++; }

?>

                                <div class="col-lg-12 col-md-12">
                                    <h1 class="dash__h1 u-s-m-b-30">Order Details</h1>
                                    <div class="dash__box dash__box--shadow dash__box--radius dash__box--bg-white u-s-m-b-30">
                                        <div class="dash__pad-2">
                                            <div class="dash-l-r">
                                                <div>
                                                    <div class="manage-o__text-2 u-c-secondary">Order #<?php echo $order_number; ?>
                                                    
                                                        <?php 
                                                        if ($order_status === 'Canceled') {
                                                        ?>
                                                        <p style="color:red;">CANCELED</p>
                                                        <?php
                                                        }
                                                        ?>
                                                    
                                                    </div>
                                                    <div class="manage-o__text u-c-silver">Placed on <?php echo $order_date; ?></div>
                                                    <div class="manage-o__text u-c-silver">With Phone NO <?php echo $phone_number; ?></div>
                                                </div>
                                                <div>
                                                    <div class="manage-o__text-2 u-c-silver">Total:

                                                        <span class="manage-o__text-2 u-c-secondary"><?php echo number_format( $total_amount, 0 ); ?> RWF</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dash__box dash__box--shadow dash__box--radius dash__box--bg-white u-s-m-b-30">
                                        <div class="dash__pad-2">
                                            <div class="manage-o">
                                                <div class="manage-o__header u-s-m-b-30">
                                                    <div class="manage-o__icon"><i class="fas fa-box u-s-m-r-5"></i>

                                                        <span class="manage-o__text">Package(s) - <?php echo $p_count; ?></span></div>
                                                </div>
                                                <div class="dash-l-r">
                                                    <div class="manage-o__text u-c-secondary">Estimated delivery time - <?php echo $estimated_delivery_date; ?></div>
                                                    <div class="manage-o__icon"><i class="fas fa-truck u-s-m-r-5"></i>

                                                        <span class="manage-o__text">Standard</span></div>
                                                </div>
                                                <div class="manage-o__timeline">
                                                    <div class="timeline-row">

                                                        <?php 
                                                        if ($order_status === 'Processing') {
                                                        ?>
                                                        <div class="col-lg-4 u-s-m-b-30">
                                                            <div class="timeline-step">
                                                                <div class="timeline-l-i timeline-l-i--finish">

                                                                    <span class="timeline-circle"></span></div>

                                                                <span class="timeline-text">Processing</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 u-s-m-b-30">
                                                            <div class="timeline-step">
                                                                <div class="timeline-l-i timeline-l-i">

                                                                    <span class="timeline-circle"></span></div>

                                                                <span class="timeline-text">Shipped - <?php echo $shipped_date ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 u-s-m-b-30">
                                                            <div class="timeline-step">
                                                                <div class="timeline-l-i">

                                                                    <span class="timeline-circle"></span></div>

                                                                <span class="timeline-text">Delivered - <?php echo $delivered_date ?></span>
                                                            </div>
                                                        </div>



                                                        <?php 
                                                        }elseif ($order_status === 'Shipped') {
                                                        ?>
                                                        <div class="col-lg-4 u-s-m-b-30">
                                                            <div class="timeline-step">
                                                                <div class="timeline-l-i timeline-l-i--finish">

                                                                    <span class="timeline-circle"></span></div>

                                                                <span class="timeline-text">Processing</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 u-s-m-b-30">
                                                            <div class="timeline-step">
                                                                <div class="timeline-l-i timeline-l-i--finish">

                                                                    <span class="timeline-circle"></span></div>

                                                                <span class="timeline-text">Shipped - <?php echo $shipped_date ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 u-s-m-b-30">
                                                            <div class="timeline-step">
                                                                <div class="timeline-l-i">

                                                                    <span class="timeline-circle"></span></div>

                                                                <span class="timeline-text">Delivered - <?php echo $delivered_date ?></span>
                                                            </div>
                                                        </div>



                                                        <?php 
                                                        }elseif ($order_status === 'Delivered') {
                                                        ?>
                                                        <div class="col-lg-4 u-s-m-b-30">
                                                            <div class="timeline-step">
                                                                <div class="timeline-l-i timeline-l-i--finish">

                                                                    <span class="timeline-circle"></span></div>

                                                                <span class="timeline-text">Processing</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 u-s-m-b-30">
                                                            <div class="timeline-step">
                                                                <div class="timeline-l-i timeline-l-i--finish">

                                                                    <span class="timeline-circle"></span></div>

                                                                <span class="timeline-text">Shipped - <?php echo $shipped_date ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 u-s-m-b-30">
                                                            <div class="timeline-step">
                                                                <div class="timeline-l-i timeline-l-i--finish">

                                                                    <span class="timeline-circle"></span></div>

                                                                <span class="timeline-text">Delivered - <?php echo $delivered_date ?></span>
                                                            </div>
                                                        </div>


                                                        <?php
                                                        }else{
                                                        ?>
                                                        <div class="col-lg-4 u-s-m-b-30">
                                                            <div class="timeline-step">
                                                                <div class="timeline-l-i timeline-l-i">

                                                                    <span class="timeline-circle"></span></div>

                                                                <span class="timeline-text">Processing</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 u-s-m-b-30">
                                                            <div class="timeline-step">
                                                                <div class="timeline-l-i timeline-l-i">

                                                                    <span class="timeline-circle"></span></div>

                                                                <span class="timeline-text">Shipped - <?php echo $shipped_date ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 u-s-m-b-30">
                                                            <div class="timeline-step">
                                                                <div class="timeline-l-i">

                                                                    <span class="timeline-circle"></span></div>

                                                                <span class="timeline-text">Delivered - <?php echo $delivered_date ?></span>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        }
                                                        ?>




                                                         


													<?php if ($order_status === 'Processing') { ?>

													<center><div class="u-s-m-b-15">
                                        			<a href="#/" onclick="cancel_order('<?php echo $order_id; ?>')" class="f-cart__ship-link btn--e-brand-b-2" style="background:red;"> CANCEL ORDER</a>
                                    				</div><br></center>
                                                    
                                                    <?php } ?>
                                                    
                                                    <?php if ($order_status === 'Shipped') { ?>

													<center><div class="u-s-m-b-15">
                                        			<a href="#/" onclick="confirm_delivered('<?php echo $order_id; ?>')" class="f-cart__ship-link btn--e-brand-b-2"> CONFIRM DELIVERED</a>
                                    				</div><br></center>
                                                    
                                                    <?php } ?>
                                                    
                                                    <input type="text" id="result_response" style="display: none;">
                                                        





                                                    </div>
                                                </div>


<?php

            $sql12="SELECT * from cart_item where cart_id='$cart_id' and order_number='$order_number' order by register_date DESC";
            $result12=$conn->query($sql12);
            $a = 1;
            while ($row12 = $result12->fetch_assoc()) {
                $item_id=$row12['item_id'];
                $product_id=$row12['product_id'];
                $cart_item_product_quantity=$row12['product_quantity'];
                $cart_item_price=$row12['price'];
                $cart_item_register_date=$row12['register_date'];

                $id1="Mine".$a;
                $id2="Mine1".$a;
                $id3="Mine2".$a;
                $id4="Mine3".$a;

                $sql53="SELECT * from product where product_id='$product_id'";
                $result53=$conn->query($sql53);
                while ($row53 = $result53->fetch_assoc()) {
                    $product_id =$row53['product_id'];
                    $category_id=$row53['category_id'];
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

                    $sql="SELECT * from product_category where category_id='$category_id'";
                    $result=$conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        $category_name=$row['category_name'];
                    }

                }

?>


                                                <div class="manage-o__description">
                                                    <div class="description__container">


                                                        <?php
                                                        $sql="SELECT * from product_picture where product_id='$product_id' order by register_date DESC limit 1";
                                                        $result=$conn->query($sql);
                                                        while ($row = $result->fetch_assoc()) {
                                                            $picture=$row['picture'];
                                                            ?>
                                                            <div class="description__img-wrap" style="border-radius: 10%;">
                                                                <img class="u-img-fluid" src="uploads/<?php echo $picture; ?>" style='border-radius: 10%;' alt="">
                                                            </div>
                                                            
                                                        <?php } ?>
                                                        

                                                        <div class="description-title"><?php echo $product_name; ?></div>
                                                    </div>
                                                    <div class="description__info-wrap">
                                                        <div>

                                                            <span class="manage-o__text-2 u-c-silver">Quantity:

                                                                <span class="manage-o__text-2 u-c-secondary"><?php echo $cart_item_product_quantity; ?></span></span></div>
                                                        <div>

                                                            <span class="manage-o__text-2 u-c-silver">Total:

                                                                <span class="manage-o__text-2 u-c-secondary"><?php echo number_format( $cart_item_price, 0 ); ?> RWF</span></span></div>
                                                    </div>
                                                </div>

<?php $a++; } ?>


                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="dash__box dash__box--bg-white dash__box--shadow u-s-m-b-30">
                                                <div class="dash__pad-3">
                                                    <h2 class="dash__h2 u-s-m-b-8">Shipping Address</h2>
                                                    <h2 class="dash__h2 u-s-m-b-8"><?php echo $first_name." ".$last_name; ?></h2>

                                                    <span class="dash__text-2"><?php echo $province; ?>, <?php echo $district; ?>, <?php echo $sector; ?>, <?php echo $cell; ?>, <?php echo $village; ?>, <?php echo $street; ?>, <?php echo $described_address; ?></span>

                                                    <span class="dash__text-2"><?php echo $phone_no ?></span>
                                                </div>
                                            </div>
                                            <div class="dash__box dash__box--bg-white dash__box--shadow dash__box--w">
                                                <div class="dash__pad-3">
                                                    <h2 class="dash__h2 u-s-m-b-8">Billing Address</h2>
                                                    <h2 class="dash__h2 u-s-m-b-8"><?php echo $first_name." ".$last_name; ?></h2>

                                                    <span class="dash__text-2"><?php echo $phone_no ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="dash__box dash__box--bg-white dash__box--shadow u-h-100">
                                                <div class="dash__pad-3">
                                                    <h2 class="dash__h2 u-s-m-b-8">Total Summary</h2>
                                                    <div class="dash-l-r u-s-m-b-8">
                                                        <div class="manage-o__text-2 u-c-secondary">Subtotal</div>
                                                        <div class="manage-o__text-2 u-c-secondary"><?php echo number_format( $sub_total_amount, 0 ); ?> RWF</div>
                                                    </div>
                                                    <div class="dash-l-r u-s-m-b-8">
                                                        <div class="manage-o__text-2 u-c-secondary">Shipping Fee</div>
                                                        <div class="manage-o__text-2 u-c-secondary"><?php echo number_format( $shipping_amount, 0 ); ?> RWF</div>
                                                    </div>
                                                    <div class="dash-l-r u-s-m-b-8">
                                                        <div class="manage-o__text-2 u-c-secondary">Total</div>
                                                        <div class="manage-o__text-2 u-c-secondary"><?php echo number_format( $total_amount, 0 ); ?> RWF</div>
                                                    </div>

                                                    <span class="dash__text-2">Paid by <?php echo $method_name ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--====== End - Section Content ======-->
            </div>
            <!--====== End - Section 2 ======-->

        <?php }else{ ?>

            <div class="col-lg-12 col-md-12">
                <h1 class="dash__h1 u-s-m-b-30">Order Not Found</h1>
                <div class="dash__box dash__box--shadow dash__box--radius dash__box--bg-white u-s-m-b-30">
                    <div class="dash__pad-2">
                        <div class="dash-l-r">
                            <div>
                                <div class="manage-o__text-2 u-c-secondary">Order <b>#<?php echo $order_number; ?></b> with Phone Number <b><?php echo $phone_number; ?></b> Not found</div>
                            </div>
                            <div>
                                <a href="index.php?shop" style="color: #FF4500;">Return to shopping</a>
                        </div>
                    </div>
            </div></div></div></div></div></div>

            <?php } ?>