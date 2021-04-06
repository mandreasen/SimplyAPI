<?php

namespace Simply;

// Require API methods
require_once "API.php";

/**
 * Simply API client for communicating with Simply.com
 * 
 * Docs for API: https://www.simply.com/dk/docs/api/
 */
class Client extends API
{
	/**
	 * @param      string  $account  Simply.com accountname or account number
	 * @param      string  $apikey   Simply.com API key
	 */
	public function __construct(string $account, string $apikey)
	{
		parent::__construct($account, $apikey);
	}

	/**
	 * Check domain availability
	 *
	 * @param      string  $domain  The name of the domain, in the format "example.com". IDNA format recommended.
	 */
	public function checkDomain(string $domain)
	{
		$domain = trim($domain);
		$uri = "/domaincheck/".$domain;

		return $this->get($uri);
	}

	/**
	 * Returns a list of all products for the given account.
	 */
	public function getProducts()
	{
		return $this->get("/my/products");
	}

	/**
	 * Get DNS zone
	 *
	 * @param      string  $object  Product object (Domain name)
	 */
	public function getDNSZone(string $object)
	{
		$object = trim($object);
		$uri = "/my/products/".$object."/dns";

		return $this->get($uri);
	}

	/**
	 * Get all DNS records from a DNS zone
	 *
	 * @param      string  $object  Product object (Domain name)
	 */
	public function getDNSRecords(string $object)
	{
		$object = trim($object);
		$uri = "/my/products/".$object."/dns/records";

		return $this->get($uri);
	}

	/**
	 * Add a DNS record to a DNS zone
	 *
	 * @param      string   $object    Product object (Domain name)
	 * @param      string   $name      The name of the DNS record
	 * @param      string   $data      The value of the DNS record
	 * @param      string   $type      The type of DNS record to add (A, MX, CNAME etc.)
	 * @param      integer  $ttl       Optional. The amount of seconds for the TTL of the DNS record. Default: 3600
	 * @param      integer  $priority  Optional. The priority of the DNS record, where applies. Default: 0
	 */
	public function addDNSRecord(string $object, string $name, string $data, string $type, int $ttl = 3600, int $priority = 0)
	{
		$object = trim($object);
		$name = trim($name);
		$type = strtoupper(trim($type));
		$uri = "/my/products/".$object."/dns/records";

		return $this->post($uri, array(
			'name' => $name,
			'data' => $data,
			'type' => $type,
			'ttl' => $ttl,
			'priority' => $priority
		));
	}

	/**
	 * Edit a record in a DNS zone
	 *
	 * @param      string   $object     Product object (Domain name)
	 * @param      integer  $record_id  The ID of the DNS record, from the /dns/records/ call
	 * @param      string   $name       The name of the DNS record
	 * @param      string   $data       The value of the DNS record
	 * @param      string   $type       The type of DNS record to add (A, MX, CNAME etc.)
	 * @param      integer  $ttl        Optional. The amount of seconds for the TTL of the DNS record. Default: 3600
	 * @param      integer  $priority   Optional. The priority of the DNS record, where applies. Default: 0
	 */
	public function editDNSRecord(string $object, int $record_id, string $name, string $data, string $type, int $ttl = 3600, int $priority = 0)
	{
		$object = trim($object);
		$name = trim($name);
		$type = strtoupper(trim($type));
		$uri = "/my/products/".$object."/dns/records/".$record_id;

		return $this->put($uri, array(
			'name' => $name,
			'data' => $data,
			'type' => $type,
			'ttl' => $ttl,
			'priority' => $priority
		));
	}

	/**
	 * Change value of a DNS record, without knowing the record_id
	 *
	 * @param      string   $object     Product object (Domain name)
	 * @param      string   $name       The name of the DNS record
	 * @param      string   $data       The value of the DNS record
	 * @param      string   $type       The type of DNS record (A, MX, CNAME etc.)
	 */
	public function editDNSValue(string $object, string $name, string $data, string $type)
	{
		$record = $this->findDNSRecord($object, $name, $type);

		if ($record) {
			return $this->editDNSRecord($object, $record->record_id, $record->name, $data, $record->type, $record->ttl, $record->priority);
		}

		return null;
	}

	/**
	 * Delete a record from a DNS zone
	 *
	 * @param      string   $object     Product object (Domain name)
	 * @param      integer  $record_id  The ID of the DNS record, from the /dns/records/ call
	 */
	public function deleteDNSRecord(string $object, int $record_id)
	{
		$object = trim($object);
		$uri = "/my/products/".$object."/dns/records/".$record_id;

		$this->delete($uri);
	}

	/**
	 * Delete a record from a DNS zone, without knowing the record_id
	 * 
	 * @param      string   $object     Product object (Domain name)
	 * @param      string   $name       The name of the DNS record
	 * @param      string   $type       The type of DNS record (A, MX, CNAME etc.)
	 */
	public function removeDNSRecord(string $object, string $name, string $type)
	{
		$record = $this->findDNSRecord($object, $name, $type);

		if ($record) {
			return $this->deleteDNSRecord($object, $record->record_id);
		}

		return null;
	}

	/**
	 * Force a reload of the DNS zone, increasing the serial
	 *
	 * @param      string  $object  Product object (Domain name)
	 */
	public function forceReloadDNSZone(string $object)
	{
		$object = trim($object);
		$uri = "/my/products/".$object."/dns/reload";

		$this->post($uri);
	}

	/**
	 * Finds a DNS record
	 *
	 * @param      string   $object     Product object (Domain name)
	 * @param      string   $name       The name of the DNS record
	 * @param      string   $type       The type of DNS record (A, MX, CNAME etc.)
	 */
	public function findDNSRecord(string $object, string $name, string $type)
	{
		$record = null;
		$object = trim($object);
		$name = trim($name);
		$name = ($name == $object) ? "@" : str_replace(".".$object, "", $name);
		$type = strtoupper(trim($type));
		$records = $this->getDNSRecords($object);

		if ($records !== null && isset($records->records)) {
			foreach ($records->records as $obj) {
				if ($obj->name == $name && $obj->type == $type) {
					$record = $obj;
					break;
				}
			}
		}

		return $record;
	}
}

?>