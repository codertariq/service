<?php
namespace TRT\Service\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TRT\Service\Repositories\LicenseRepository;
use TRT\Service\Requests\LicenseRequest;

class LicenseController extends Controller {
	protected $request;
	protected $repo;

	public function __construct(
		Request $request,
		LicenseRepository $repo
	) {
		$this->request = $request;
		$this->repo = $repo;
	}

	public function verify(LicenseRequest $request) {
		$this->repo->verify($this->request->all());

		return $this->success(['message' => trans('install.verified')]);
	}
}
