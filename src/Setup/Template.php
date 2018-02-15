<?php
namespace Civi\Setup;

class Template {

  protected $filetype;

  protected $smarty;
  protected $beautifier;

  public function __construct($srcPath, $fileType) {
    $this->filetype = $filetype;

    $this->smarty = \Civi\Setup\SmartyUtil::createSmarty($srcPath);

    $this->assign('generated', "DO NOT EDIT.  Generated by Installer");

    if ($this->filetype === 'php') {
      require_once 'PHP/Beautifier.php';
      // create an instance
      $this->beautifier = new PHP_Beautifier();
      $this->beautifier->addFilter('ArrayNested');
      // add one or more filters
      $this->beautifier->setIndentChar(' ');
      $this->beautifier->setIndentNumber(2);
      $this->beautifier->setNewLine("\n");
    }
  }

}