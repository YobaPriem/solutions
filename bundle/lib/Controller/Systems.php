<?php

namespace App\Bundle\SystemsSelectionV2\Controller;

use Bitrix\Main\Application;
use Bitrix\Main\Mail\Event;
use TAO;
use TAO\Controller;
use App\Sms;
use App\Sms\SmscApi;

class Systems extends Controller
{
	/**
	 * @var \Bitrix\Main\HttpRequest
	 */
	protected $request;

	public function __construct()
	{
		try {
			$this->request = Application::getInstance()->getContext()->getRequest();
		} catch (\Exception $e) {

		}
	}

	public function loadJSON()
	{
		$path = $_SERVER['DOCUMENT_ROOT'] . '/upload/system-selection.json';

		if (file_exists($path)) {
			$fileContent = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/upload/system-selection.json');

			return $this->renderJSON(json_decode($fileContent));
		}

		http_response_code(404);
		return $this->renderJSON('', false);
	}

	public function loadSystems()
	{
		$ids = $this->request['ids'];

		$systems = [];

		$systemsEntities = TAO::infoblock('systems')->getItems([
			'filter' => [
				'ACTIVE' => 'Y',
				'ID' => $ids
			],
		]);

		foreach ($systemsEntities as $entity) {
			$blueprints = [];

			if ($entity->property('BLUEPRINT')->value()) {
				$blueprints['dwg'] = $this->getFileBasicInfo($entity->property('BLUEPRINT')->value(), 'Чертежи .dwg');
				$blueprints['pdf'] = $this->getFileBasicInfo($entity->property('BLUEPRINT_PDF')->value(), 'Чертежи');
			}
			$systems[] = [
				'id' => $entity->id(),
				'title' => $entity->title(),
				'text' => $entity['PREVIEW_TEXT'],
				'images' => [
					'preview' => \CFile::GetPath($entity['PREVIEW_PICTURE']),
					'detail' => \CFile::GetPath($entity['DETAIL_PICTURE'])
				],
				'url' => [
					'detail' => $this->getSiteDomain() . $entity['DETAIL_PAGE_URL'],
					'shop' => $entity->property('SHOP_LINK')->value(),
				],
				'document' => $this->getDocument($entity->property('DOCUMENTS')->value()),
				'blueprints' => $blueprints,
				'price' => $entity->property('PRICE')->value(),
				'materials' => $this->getProducts($entity->property('PRODUCTS')->value()),
				'bim' => $this->getBIMFilter($entity->property('bim')->value())
			];
		}

		return $this->renderJSON($systems);
	}

	private function getSiteDomain()
	{
		$isHttps = !empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']);

		return ($isHttps ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'];
	}

	private function getProducts($ids)
	{
		$products = [];

		$productsEntities = TAO::infoblock('systems_products')->getItems([
			'filter' => [
				'ACTIVE' => 'Y',
				'PROPERTY_IS_MAIN_VALUE' => 'Да',
				'ID' => $ids
			],
		]);

		foreach ($productsEntities as $entity) {
			$products[] = [
				'title' => $entity->title()
			];
		}

		return $products;
	}

	private function generateCode()
	{
		$code = rand(1000,9999);

		session_start();
		$_SESSION['code'] = $code;

		return $code;
	}

	public function verifyCode()
	{
		$isValid = false;

		$code = $this->request['code'];

		if (intval($code) === $_SESSION['code']) {
			$isValid = true;
		}

		return $this->renderJSON($isValid);
	}

	public function getCode()
	{
		$code = $this->generateCode();
		$phone = $this->request['phone'];
		$isValid = false;

		// $response = 'OK - 1 SMS, ID - 53233';

		$response = SmscApi::send(str_replace(['+', '-', '(', ')', '_', ' '], '', $phone), 'Ваш код подтверждения: ' . $code);

		if (preg_match('/OK/', $response)) {
			$isValid = true;
		}

		return $this->renderJSON($response, $isValid);
	}

	public function submitForm()
	{
		$params = $this->request['params'];
		$systemsIds = [];


		$props = array(
			'name' => $params['name'],
			'phone' => $params['phone'],
			'email' => $params['email'],
			'object_name'=> $params['object']['name'],
			'object_address' => $params['object']['address'],
			'history' => json_decode($params['history']),
			'systems' => array_column(json_decode($params['systems']), 'id'),
		);

		$element = new \CIBlockElement;

		$arElement = [
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID" => \TAO::infoblock('systems-selection-v2')->getId(),
			"ACTIVE" => "Y",
			"NAME" => 'Заявка с формы подбор систем:' . $params['name'] . '. Объект:' . $params['object']['name'],
			"PROPERTY_VALUES"=> $props,
		];

		$elementId = $element->add($arElement);

		Event::send(
			array(
				"EVENT_NAME" => "SYSTEM_SELECTION_V2",
				"LID" => "s1",
				"C_FIELDS" => $props,
			)
		);

		return $this->renderJSON(
			[
				'elementID' => $elementId
			]
		);
	}

	private function getDocument($documentsIDs)
	{
		 /*
		вроде как тех лист всегда первым идет, поэтому пока так
		¯\_(ツ)_/¯. Если что, надо будет получить итем документов и по его свойству отловить тех лист.
		*/
		$id = array_shift($documentsIDs);

		$entity = \TAO::infoblock('documents')->loadItem($id);

		$file = $entity->property('FILE')->value();

		return $this->getFileBasicInfo($file, 'Технический лист');
	}

	private function getFileBasicInfo($file, $title = '')
	{
		$info = [];

		if ($file) {
			$fullFilename = $file->fieldsData['ORIGINAL_NAME'];

			preg_match('/\\.[^.\\s]{3,4}$/', $fullFilename, $matches);

			$info = [
				'url' => '/upload/' . $file->fieldsData['SUBDIR'] . '/' . $file->fieldsData['FILE_NAME'],
				'extension' => $matches[0],
				'filesize' => get_human_file_size($file->fieldsData['FILE_SIZE']),
				'title' => $title
			];
		}

		return $info;
	}

	private function getBIMFilter($sectionID)
	{
		$section = TAO::infoblock('bim')->getSectionById($sectionID);

		return '/bim/filter/programm-is-' . $section['CODE'] . '/apply/';
	}

	protected function renderJSON($value = '', $success = true)
	{
		$this->noLayout();
		header('Content-Type: application/json');
		echo json_encode(
			[ 'success' => $success, 'dataSet' => $value ],
			JSON_UNESCAPED_UNICODE
		);
		die;
	}
}
