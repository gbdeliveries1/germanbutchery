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

                                        <a href="index.php?wishlist">Wishlist</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--====== End - Section 1 ======-->




<?php

    $sql3 = "SELECT * from wishlist where customer_id='$customer_temp_id'";
    $result3 = $conn->query($sql3);

    $wishlist_id = '0';
    while ($row3 = $result3->fetch_assoc()) {
        $wishlist_id = $row3['wishlist_id'];
    }

    $query=$conn->query("SELECT * from wishlist_item where wishlist_id='$wishlist_id'");

    if ($wishlist_id !== '0'  && $query->num_rows >= 1) {

        ?>

            <!--====== Section 2 ======-->
            <div class="u-s-p-b-60">

                <!--====== Section Intro ======-->
                <div class="section__intro u-s-m-b-60">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="section__text-wrap">
                                    <h1 class="section__heading u-c-secondary">WISHLIST</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--====== End - Section Intro ======-->

                <!--====== Section Content ======-->
                <div class="section__content">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 u-s-m-b-30">
                                <div class="table-responsive">
                                    <table class="table-p">
                                        <tbody>

            <?php

            $sql12="SELECT * from wishlist_item where wishlist_id='$wishlist_id' order by register_date DESC";
            $result12=$conn->query($sql12);
            $a = 1;
            while ($row12 = $result12->fetch_assoc()) {
                $item_id=$row12['item_id'];
                $product_id=$row12['product_id'];
                $wishlist_item_register_date=$row12['register_date'];

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



                                            <!--====== Row ======-->
                                            <tr>
                                                <td>
                                                    <div class="table-p__box">

                        <?php
                        $sql="SELECT * from product_picture where product_id='$product_id' order by register_date DESC limit 1";
                        $result=$conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            $picture=$row['picture'];

                            ?>
                            <div class="table-p__img-wrap">
                                <img class="u-img-fluid" src="uploads/<?php echo $picture; ?>" alt="">
                            </div>
                        <?php } ?>

                                                        <div class="table-p__info">

                                                            <span class="table-p__name">

                                                                <a href="index.php?product-detail&product=<?php echo $product_id ?>"><?php echo $product_name ?></a></span>
                                                                <input type="text" id="<?php echo $id1 ?>" value="<?php echo $item_id ?>" style='display: none;'>

                                                            <span class="table-p__category">

                                                                <a href="index.php?shop"><?php echo $category_name ?></a></span>
                                                            <ul class="table-p__variant-list">
                                                                <li>

                                                                    <span>Quantity in stock: <?php echo $product_quantity ?></span></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>

                                                    <span class="table-p__price">Price: <?php echo number_format( $product_price, 0 ); ?> RWF</span></td>
                                                
                                                <td>
                                                    <div class="table-p__del-wrap">

                                                        <a class="far fa-trash-alt table-p__delete-link" href="#/" onclick="remove_from_wishlist('<?php echo $item_id ?>');"></a></div>
                                                </td>
                                            </tr>
                                            <!--====== End - Row ======-->

                                <?php $a++; } ?>


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        	<input type='text' style='display:none;' id='result_response'>
                            <div class="col-lg-12">
                                <div class="route-box">
                                    <div class="route-box__g1">

                                        <a class="route-box__link" href="index.php?shop"><i class="fas fa-long-arrow-alt-left"></i>

                                            <span>CONTINUE SHOPPING</span></a></div>
                                    <!--<div class="route-box__g2">

                                        <a class="route-box__link" href="#/" onclick="clear_cart('<?php echo $cart_id ?>');"><i class="fas fa-trash"></i>

                                            <span>CLEAR CART</span></a>

                                        <a class="route-box__link" href="#/" onclick="location.reload();"><i class="fas fa-sync"></i>

                                            <span>UPDATE CART</span></a></div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--====== End - Section Content ======-->

            </div>
            <!--====== End - Section 2 ======-->



                            <?php }else{ ?>

                                <div class="section__content">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 u-s-m-b-30">
                                                <div class="empty">
                                                    <div class="empty__wrap">

                                                        <span class="empty__big-text">EMPTY</span>

                                                        <span class="empty__text-1">No items found on your wishlist.</span>

                                                        <a class="empty__redirect-link btn--e-brand" href="index.php?shop">CONTINUE SHOPPING</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php } ?>

