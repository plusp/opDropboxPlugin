<?php

/**
 * dropbox actions.
 *
 * @package    OpenPNE
 * @subpackage dropbox
 * @author     Your name here
 */
class fActions extends sfActions
{

 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $path = $request->getParameter("path");

    if(!$path){
      return $this->renderJSON(array('status' => 'error','message' => "no path"));
    }

/*
    $member_id = $this->getUser()->getMember()->getId();
    if(strpos($path, "/m".$member_id, 0) === 0){
      //PASS
    }else{
      return $this->renderJSON(array('status' => 'error','message' => "you have no permission"));
    }
*/

    $file = Doctrine::getTable("File")->findOneByName($path);
    $filebin = $file->getFileBin();
    $data = $filebin->getBin();

    if(!$data){
      return $this->renderJSON(array('status' => 'error','message' => "datafile not found"));
    }


    $filename = $file->original_filename;


    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->buffer($data);
    $this->getResponse()->setHttpHeader('Content-Type',$type);
    //if(strpos($type,'application') !== FALSE || $type == "text/x-php"){
      $this->getResponse()->setHttpHeader('Content-Disposition','attachment; filename="'.$filename.'"');
    //}
    return $this->renderText($data);
  }
}
