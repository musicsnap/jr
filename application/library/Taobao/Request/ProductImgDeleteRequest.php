<?php

/**
 * TOP API: taobao.product.img.delete request
 *
 * @author auto create
 * @since  1.0, 2013-09-13 16:51:03
 */
class Taobao_Request_ProductImgDeleteRequest {
	/**
	 * 非主图ID
	 **/
	private $id;

	/**
	 * 产品ID.Product的id,通过taobao.product.add接口新增产品的时候会返回id.
	 **/
	private $productId;

	private $apiParas = array();

	public function setId($id) {
		$this->id = $id;
		$this->apiParas["id"] = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setProductId($productId) {
		$this->productId = $productId;
		$this->apiParas["product_id"] = $productId;
	}

	public function getProductId() {
		return $this->productId;
	}

	public function getApiMethodName() {
		return "taobao.product.img.delete";
	}

	public function getApiParas() {
		return $this->apiParas;
	}

	public function check() {

		Taobao_RequestCheckUtil::checkNotNull($this->id, "id");
		Taobao_RequestCheckUtil::checkNotNull($this->productId, "productId");
	}

	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
