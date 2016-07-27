<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <?php
      require('file_manager.php');
      isset($_GET['cat']) ? $path = $_GET['cat'] : $path = dirname(__FILE__);

      $order = array('name' => 'desc',
                     'type' => 'desc',
                     'size' => 'desc');
    ?>
  <title>Test File Manager</title>
</head>
<body>
  <p>
    <?php
      echo "<p>Current folder:   ".$path."</p>";

      if ($path !== dirname(__FILE__))
      {
        $back = $_SERVER['PHP_SELF']."?cat=".realpath($_GET['cat'].'/../');
        echo "<a href=".$back.">Back</a>";
      }

      if (isset($_GET['sort']))
      {
        ($_GET['order'] === 'desc') ? $order[$_GET['sort']] = 'asc' : $orderName = 'desc';
      }
    ?>
  </p>
  <table>
    <tr>
      <th><a href="<?php echo $_SERVER['PHP_SELF'].'?sort=name&order='.$order['name'].'&cat='.realpath($_GET['cat']);?>">Name</a></th>
      <th><a href="<?php echo $_SERVER['PHP_SELF'].'?sort=type&order='.$order['type'].'&cat='.realpath($_GET['cat']);?>">Type</a></th>
      <th><a href="<?php echo $_SERVER['PHP_SELF'].'?sort=size&order='.$order['size'].'&cat='.realpath($_GET['cat']);?>">Size</a></th>
    </tr>
      <?php if ($files == null)
      {?>
        <tr>
          <td>No files</td>
        </tr>
      <?php
      }
      else
      {
        foreach($files as $file): ?>
        <tr>
          <td>
            <?php
              if ($file['type'] === 'dir')
              {
                $link = $_SERVER['PHP_SELF']."?cat=".$path.'/'.$file['name'];
                echo "<a href=".$link.">".$file['name']."</a>";
              }
              else
              {
                echo $file['name'].'.'.$file['extension'];
              }
            ?>
          </td>
          <td><?php echo $file['type']; ?></td>
          <td><?php echo $file['size'].' kB'; ?></td>
        </tr>
      <?php endforeach; }?>
    </table>
</body>
</html>
