<?php
if (isset($email)) :
?>

    <div>Data Source: <?php if (isset($datasource)) echo $datasource; ?> </div>
    <br>
    <div>Name: <?php if (isset($name)) echo $name; ?> </div>
    <br>
    <div>Email: <?php if (isset($email)) echo $email; ?> </div>
    <br>
    <div>New Data: <?php if (isset($newdata)) echo $newdata; ?> </div>
    <br>
    <div>All Data: <?php if (isset($alldata)) echo $alldata; ?> </div>
<?php else : ?>

    <div>User not logged in! Please login to see your data.</div>

<?php endif; ?>