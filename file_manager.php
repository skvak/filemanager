<?php

  trait Singleton
  {
    static private $instance = null;

    static public function getInstance()
    {
      return
      self::$instance === null ? self::$instance = new static() : self::$instance;
    }
  }

  class Filemanager()
  {
    use Singleton;

    public function get_files($directory)
    {
      $files = array();

      if ($directory[strlen($directory)-1] != '/') $directory .= '/';

      if ($catalog = opendir($directory))
      {
        while (false !== ($item = readdir($catalog)))
        {
          if (!in_array($item, array(".","..")))
          {
            $file = $directory.$item;
            $fileName = pathinfo($item);
            $fileSize = filesize($file) / 8;
            $fileType = filetype($file);
            if ($fileType === 'dir')
            {
              $path = $directory.$fileName['filename'];
            }
            $fileData = array(
                          'name' => $fileName['filename'],
                          'extension' => $fileName['extension'],
                          'size' => $fileSize,
                          'type' => $fileType,
                          'path' => $path,
                          );
            $files[] = $fileData;
          }
        }
      }
      closedir($catalog);

      return $files;
    }

    public function MultiSort($data, $sortCriteria, $caseInSensitive = true)
    {
      if( !is_array($data) || !is_array($sortCriteria))
        return false;
      $args = array();
      $i = 0;

      foreach($sortCriteria as $sortColumn => $sortAttributes)
      {
        $colList = array();
        foreach ($data as $key => $row)
        {
          $convertToLower = $caseInSensitive && (in_array(SORT_STRING, $sortAttributes) || in_array(SORT_REGULAR, $sortAttributes));
          $rowData = $convertToLower ? strtolower($row[$sortColumn]) : $row[$sortColumn];
          $colLists[$sortColumn][$key] = $rowData;
        }
        $args[] = &$colLists[$sortColumn];

        foreach($sortAttributes as $sortAttribute)
        {
          $tmp[$i] = $sortAttribute;
          $args[] = &$tmp[$i];
          $i++;
         }
      }
      $args[] = &$data;
      call_user_func_array('array_multisort', $args);
      return end($args);
    }

    public function GetSortCriteria()
    {
      $sortCriteria['namedesc'] = array('type' => array(SORT_ASC),
                                    'name' => array(SORT_DESC, SORT_NATURAL));
      $sortCriteria['nameasc'] = array('type' => array(SORT_ASC),
                                       'name' => array(SORT_ASC, SORT_NATURAL));
      $sortCriteria['sizedesc'] = array('type' => array(SORT_ASC),
                                        'size' => array(SORT_DESC, SORT_NATURAL));
      $sortCriteria['sizeasc'] = array('type' => array(SORT_ASC),
                                       'size' => array(SORT_ASC, SORT_NATURAL));
      $sortCriteria['typedesc'] = array('type' => array(SORT_DESC));
      $sortCriteria['typeasc'] = array('type' => array(SORT_ASC));

      return $sortCriteria;
    }
  }

  isset($_GET['cat']) ? $cat = $_GET['cat'] : $cat = getcwd();

  isset($_GET['sort']) ? $sort = $_GET['sort'] : $sort = 'name';

  isset($_GET['order']) ? $order = $_GET['order'] : $order = 'desc';

  $sort = $sort.$order;


  $filemanager = Filemanager::getInstance();

  $files = $filemanager->get_files($cat);

  var_dump($filemanager->get_files($cat));die;

  $sortCriteria = $filemanager->GetSortCriteria();

  $files = $filemanager->MultiSort($files, $sortCriteria[$sort], true);

?>
