<?php
include 'DBConnection.php'; // Include the database connection

?>
<h3>Welcome to Simple Online Groceries Ordering System</h3>
<hr>
<style>
    .prod-item:hover>.card{
        background: rgba(var(--bs-info-rgb),.5)
    }
    
    .prod-item>.card .img-top{
        transition: transform .5s ease;
        width: 100%;
    }
    .prod-item:hover>.card .img-top{
        transform:scale(1.5);
    }
</style>
<div class="col-lg-12">
    <div class="row">
        <div class="col-md-4">
            <h4><b>Categories</b></h4>
            <hr>
            <div class="list-group">
                <a href="./?category=all" class="list-group-item <?php echo !isset($_GET['category']) || (isset($_GET['category']) && !is_numeric($_GET['category']))? "active" : "" ?>">All</a>
                <?php 
                // Fetching categories from the database
                $categories = $conn->query("SELECT * FROM `category_list` WHERE `status` = 1 ORDER BY `name` ASC");
                while($row=$categories->fetch_assoc()): // Use fetch_assoc() for associative array
                ?>
                    <a href="./?category=<?php echo $row['category_id'] ?>" class="list-group-item <?php echo isset($_GET['category']) && $_GET['category'] == $row['category_id'] ? "active" : "" ?>"><?php echo $row['name'] ?></a>
                <?php endwhile; ?>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-12">
                    <div class="input-group mb-3">
                        <input type="text" name="search" value="<?php echo isset($_GET['search'])?  $_GET['search'] : '' ?>" class="form-control" placeholder="Search Product Here" aria-label="Search Product Here" aria-describedby="button-addon2">
                        <button class="btn btn-outline-primary" type="button" id="search">Search</button>
                    </div>
                </div>
            </div>
            <div class="row gx-3 row-cols-3">
                <?php 
                $where = "";
                if(isset($_GET['category']) && $_GET['category'] != 'all' && is_numeric($_GET['category'])){
                    $where = " AND p.`category_id` = '{$_GET['category']}' ";
                }
                if(isset($_GET['search'])){
                    $where = " AND (p.`name` LIKE '%{$_GET['search']}%' OR p.`description` LIKE '%{$_GET['search']}%' OR c.`name` LIKE '%{$_GET['search']}%') ";
                }
                // Fetching products from the database
                $qry = $conn->query("SELECT p.*,c.name AS cname FROM `product_list` p INNER JOIN `category_list` c ON p.category_id = c.category_id WHERE p.`status` = 1 {$where} ORDER BY p.`name` ASC");
                while($row = $qry->fetch_assoc()): // Use fetch_assoc() for associative array
                ?>
                <a class="col prod-item text-dark text-decoration-none" href="javascript:void(0)" data-id="<?php echo $row['product_id'] ?>">
                    <div class="card h-100">
                        <div class="h-auto overflow-hidden">
                            <img src="<?php echo "uploads/thumbnails/".$row['product_id'].".png" ?>" alt="IMG" class="img-top">
                        </div>
                        <div class="card-body">
                            <div class="fs-5"><?php echo $row['name'] ?></div>
                            <small><i><?php echo $row['cname'] ?></i></small>
                            <p class="m-0 truncate-3"><small><i><?php echo $row['description'] ?></i></small></p>
                        </div>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
            <?php 
            if(!$qry->num_rows): // Check if there are any rows
            ?>
            <center>
                <?php if(isset($_GET['search'])): ?>
                <div class="fs-5"><b><i>No product found.</i></b></div>
                <?php else: ?>
                <div class="fs-5"><b><i>No Product Listed Yet.</i></b></div>
                <?php endif; ?>
            </center>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.prod-item').click(function(){
            uni_modal("Product Details","view_product.php?id="+$(this).attr('data-id'),"mid-large")
        })
        $('[name="search"]').keydown(function(e){
            if(e.which == 13){
                e.preventDefault();
                $('#search').trigger('click')
            }
        })
        $('#search').click(function(){
            if($('[name="search"]').val() == '')
            location.href="./";
            else
            location.href="./?search="+$('[name="search"]').val();
        })
    })
</script>
