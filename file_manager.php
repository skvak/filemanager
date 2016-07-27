<?php

  trait Singleton
  {
    static private $instance = null;

    static public function getInstance($cat)
    {
      return
      self::$instance === null ? self::$instance = new static($cat) : self::$instance;
    }
  }

  class Filemanager
  {
    use Singleton;

    private function __construct($cat)
    {
      $this->files = $this->get_files($cat);
    }

    /**
     * Method that make array with data about files and dir's in $directory
     * @param  string  $directory input directory
     * @return array  $files  multidimensional array of files and dir's in input directory
     */
    public function get_files($directory)
    {
      $files = array();

      if ($directory[strlen($directory)-1] !== '/') $directory .= '/';

      if ($catalog = opendir($directory))
      {
        while (false !== ($item = readdir($catalog)))
        {
          if (!in_array($item, array(".","..",".git")))
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

    /**
     * Method that sort $files array
     * @param  array  $data input array for sorting
     * @param  array  $sortCriteria criteries for sorting data
     * @param  bool   $caseInSensitive enter sort criteria - sensitive or not to data register
     * @return array  $files  sorted multidimensional array
     */
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
          $convertToLower = $caseInSensitive
                            && (in_array(SORT_STRING, $sortAttributes)
                            || in_array(SORT_REGULAR, $sortAttributes));

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

    /**
     * Sorting criterias
     * @return array  $sortCriteria criteries for sorting data
     */
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
?>
