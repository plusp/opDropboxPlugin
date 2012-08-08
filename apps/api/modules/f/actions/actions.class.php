<?php
class fActions extends opJsonApiActions
{
  public function executeUpload(sfWebRequest $request)
  {
    $filename = basename($_FILES['upfile']['name']);
    if(!$filename){
      return $this->renderJSON(array('status' => 'error' , 'message' => "null file"));
    }

     $community_id = (int)$request->getParameter("community_id");
    if((int)$community_id >= 1){
      $community = Doctrine::getTable("Community")->find($community_id);
      if(!$community->isPrivilegeBelong($this->getUser()->getMember()->getId())){
        return $this->renderJSON(array('status' => 'error' ,'message' => "you are not this community member."));
      }
      $dirname = '/c'. $community_id;
    }else{
      $dirname = '/m'. $this->getUser()->getMember()->getId();
    }

    //validate $filepath
    if(!preg_match('/^\/[mc][0-9]+/',$dirname)){
      return $this->renderJSON(array('status' => 'error' ,'message' => "file path error. " . $dirname));
    }
    
    $f = new File();
    $f->setOriginalFilename($_FILES['upfile']['name']);
    $f->setType($_FILES['upfile']['type']);
    $f->setName($dirname."/".time().$_FILES['upfile']['tmp_name']);
    $f->setFilesize($_FILES['upfile']['size']);
    if($stream = fopen($_FILES['upfile']['tmp_name'],'r')){
      $bin = new FileBin();
      $bin->setBin(stream_get_contents($stream));
      $f->setFileBin($bin);
      $f->save();
      $response = true;
    }else{
      //file open error
      $response = false;
    }



   $file_url = Doctrine_Query::create()
      ->from('File f')
      ->where('f.name LIKE ?','/m'.$this->getUser()->getMember()->getId().'/%')
      ->orderBy('f.created_at desc')
      ->limit(1)
      ->fetchArray();

    if($response === true){
      return $this->renderJSON(array('status' => 'success' , 'message' => "file up success " . $response , 'url' =>  $file_url  )  );
    }else{
      return $this->renderJSON(array('status' => 'error','message' => "Dropbox file upload error", 'url' => '') );
    }


  }
  public function executeList(sfWebRequest $request)
  {
    $file_list = Doctrine_Query::create()
      ->from('File f')
      ->where('f.name LIKE ?','/m'.$this->getUser()->getMember()->getId().'/%')
      ->fetchArray();
    return $this->renderJSON(array('status' => 'success','data'=>$file_list));
  }
  public function executeDelete(sfWebRequest $request)
  {
    $name = $request->getParameter("name");

   $file_url = Doctrine_Query::create()
      ->delete()
      ->from('File f')
      ->where('f.name LIKE ?',$name)
      ->execute();
 
      return $this->renderJSON(array('status' => 'success' , 'message' => "file up success "   )  );

  }  

}
