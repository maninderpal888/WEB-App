<div class="sidebar">
        <!-- insert your sidebar items here -->

        <?php 
        include "checksession.php";
        if (isset($_SESSION['username'])){
          if (isset($_POST['logout'])) logout();

          $un = $_SESSION['username'];
          if($_SESSION['loggedin'] == 1){ ?>
           
          <h6>Logged in as <?php echo $un ?></h6>
          <form method="post">
          <input  type="submit" name="logout" value="Logout"> 
          </form>
	
          <?php 
          }
        }
        ?>

        <h3>Latest News</h3>
        <h4>New Web applicaiton Launched</h4>
        
        <h3>Useful Links</h3>
        <ul>
          <li><a href="https://www.whitecliffe.ac.nz/technology">Whitecliffe Tech</a></li>
          <li><a href="https://cpp.iqualify.com/login">iQualify</a></li>
          <!--<li><a href="#">no link </a></li>-->
          <li><a href="/bnb/privacy.php">Privacy statement</a></li>
        </ul>
        <h3>Search</h3>
        <form method="post" action="#" id="search_form">
          <p>
            <input class="search" type="text" name="search_field" value="Enter keywords....." />
            <input name="search" type="image" style="border: 0; margin: 0 0 -9px 5px;" src="converted template /style/search.png" alt="Search" title="Search" />
          </p>
        </form>
      </div>