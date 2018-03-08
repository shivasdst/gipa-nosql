<?php

class data extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function buildDBFromJson() {

		$this->insertForeignKeys();

		$jsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL, $pattern = '/index.json$/i');
		
		$db = $this->model->db->useDB();
		$collection = $this->model->db->createCollection($db, ARTEFACT_COLLECTION);

		$foreignKeys = $this->model->getForeignKeyTypes($db);

		foreach ($jsonFiles as $jsonFile) {

			$content = $this->model->getArtefactFromJsonPath($jsonFile);
			$content = $this->model->insertForeignKeyDetails($db, $content, $foreignKeys);
			$content = $this->model->insertDataExistsFlag($content);
			$content = $this->model->beforeDbUpdate($content);

			$result = $collection->insertOne($content);
		}

		// Insert fulltext
		$this->insertFulltext();
	}
	
	private function insertForeignKeys() {

		$jsonFiles = $this->model->getFilesIteratively(PHY_FOREIGN_KEYS_URL, $pattern = '/.json$/i');
		
		$db = $this->model->db->useDB();
		$collection = $this->model->db->createCollection($db, FOREIGN_KEY_COLLECTION);

		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);
			$content = $this->model->beforeDbUpdate($content);

			$result = $collection->insertOne($content);
		}
	}

	public function insertFulltext() {

		$txtFiles = $this->model->getFilesIteratively(PHY_METADATA_URL, $pattern = '/\/text\/\d+\.txt$/i');

		$db = $this->model->db->useDB();
		$collection = $this->model->db->createCollection($db, FULLTEXT_COLLECTION);

		foreach ($txtFiles as $txtFile) {

			$content['text'] = file_get_contents($txtFile);
			$content['text'] = $this->model->processFulltext($content['text']);
			
			$txtFile = str_replace(PHY_METADATA_URL, '', $txtFile);
			preg_match('/^(.*)\/text\/(.*)\.txt/', $txtFile, $matches);

			$content['id'] = $matches[1];
			$content['page'] = $matches[2];

			$content = $this->model->beforeDbUpdate($content);
			$result = $collection->insertOne($content);
		}
	}

	// Use this method for global changes in json files
	public function modify() {

		// $db = $this->model->db->useDB();
		// $collection = $this->model->db->selectCollection($db, ARTEFACT_COLLECTION);

		// $iterator = $collection->distinct("State", ["Type" => "Brochure"]);

		// $data = [];
		// foreach ($iterator as $state) {
			
		// 	$Places = $collection->distinct("Place", ["State" => $state]);
		// 	$data[$state][] = $Places;
		// }
		// file_put_contents("StatePlaces.txt", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

		$contentString = file_get_contents('/home/sriranga/Desktop/dvg-ebooks/json-precast/book-details.json');
		$raw = json_decode($contentString, true);

		foreach ($raw as $key => $booksID) {
			
			foreach ($booksID as $id => $content) {
				$data = [];
				$content['Collection'] = 'DVG Collection';
				$content['Type'] = 'Ebook';
				$content['BookID'] = $id;
				$content['id'] = '001/001/' . $id;
				exec('mkdir -p ' . PHY_METADATA_URL . $content['id']);
				($content['subtitle'] != '')? $content['title'] .=  ' ' . $content['subtitle'] : '';
				unset($content['subtitle']);
				unset($content['volume']);

				$data['id'] = $content['id'];
				$data['Title'] = $content['title'];
				$data['Author'] = 'ಡಿ. ವಿ. ಗುಂಡಪ್ಪ';
				$data['Editor'] = 'ಹಾ. ಮಾ. ನಾಯಕ';
				$data['Collection'] = $content['Collection'];
				$data['Type'] = $content['Type'];
				$data['Language'] = $content['language'];
				$data['Bookid'] = $content['BookID'];
				$data['Amount'] = '100.00';
				$data['Datepublished'] = '2018';
				$data['Publisher'] = 'Sriranga Digital Software Technologies';
				$data['Identifier'] = $content['identifier'];
				$data['Description'] = $content['description'];
				$data['Creator'] = 'Sriranga Digital Software Technologies';

				$json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
				file_put_contents(PHY_METADATA_URL . '001/001/' . $id . '/index.json', $json);
			}
		}
	}

	public function bulkReplaceAction() {
		
		// Get post data	
		$data = $this->model->getPostData();

		$metaDataJsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL  , $pattern = '/index.json$/i');
		$foreignKeyJsonFiles = $this->model->getFilesIteratively(PHY_FOREIGN_KEYS_URL , $pattern = '/json$/i');
		
		$jsonFiles = array_merge($metaDataJsonFiles, $foreignKeyJsonFiles);

		$resultBoolean = True;
		$affectedFiles = [];
		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);
			
			if(isset($content[$data['key']])) {

				if($content[$data['key']] == $data['oldValue']) { 

					$content[$data['key']] = $data['newValue'];
					
					if(!(@$this->model->writeJsonToPath($content, $jsonFile))){

						$resultBoolean = False;
						break;
					}
					array_push($affectedFiles, $jsonFile);
				}
			}
		}

		if($resultBoolean){

			$this->buildDBFromJson();
			$this->redirect('gitcvs/updateRepo');
		}
		else{

			require_once 'application/controllers/gitcvs.php';

			$gitcvs = new gitcvs;
			$gitcvs->checkoutFiles($affectedFiles);
			$this->view('error/prompt',["msg"=>"Problem in writing data to file"]); return;
		}
	}
}

?>