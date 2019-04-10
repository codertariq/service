<?php
namespace TRT\Service\Repositories;

use Illuminate\Validation\ValidationException;

class InitRepository {
	public function init() {
		config(['app.item' => '78966']);
		config(['app.verifier' => 'https://auth.satt.dev']);
		config(['app.helpdoc' => 'https://help.satt.dev']);
	}

	public function check() {
		if (isTestMode()) {
			return;
		}

		if (\Storage::exists('.access_log') && \Storage::get('.access_log') == date('Y-m-d')) {
			return;
		}

		if (!isConnected()) {
			return;
		}

		$ac = \Storage::exists('.access_code') ? \Storage::get('.access_code') : null;
		$e = \Storage::exists('.account_email') ? \Storage::get('.account_email') : null;
		$c = \Storage::exists('.app_installed') ? \Storage::get('.app_installed') : null;
		$v = \Storage::exists('.version') ? \Storage::get('.version') : null;

		$url = config('app.verifier') . '/api/cc?a=verify&u=' . url()->current() . '&ac=' . $ac . '&i=' . config('app.item') . '&e=' . $e . '&c=' . $c . '&v=' . $v;
		$response = curlIt($url);

		$status = (isset($response['status']) && $response['status']) ? 1 : 0;

		if (!$status) {
			\Storage::delete(['.access_code', '.account_email']);
			\Storage::put('.app_installed', '');
		} else {
			\Storage::put('.access_log', date('Y-m-d'));
		}
	}

	public function product() {
		if (!isConnected()) {
			throw ValidationException::withMessages(['message' => 'No internect connection.']);
		}

		$ac = \Storage::exists('.access_code') ? \Storage::get('.access_code') : null;
		$e = \Storage::exists('.account_email') ? \Storage::get('.account_email') : null;
		$c = \Storage::exists('.app_installed') ? \Storage::get('.app_installed') : null;
		$v = \Storage::exists('.version') ? \Storage::get('.version') : null;
		$ex = \Storage::exists('.expired') ? \Storage::get('.expired') : null;

		$about = file_get_contents(config('app.verifier') . '/about');
		$update_tips = file_get_contents(config('app.verifier') . '/update-tips');
		$support_tips = file_get_contents(config('app.verifier') . '/support-tips');

		$url = config('app.verifier') . '/api/cc?a=product&u=' . url()->current() . '&ac=' . $ac . '&i=' . config('app.item') . '&e=' . $e . '&c=' . $c . '&v=' . $v . '&ex=' . $ex;
		$response = curlIt($url);

		$status = (isset($response['status']) && $response['status']) ? 1 : 0;

		if (!$status) {
			$message = isset($response['message']) ? $response['message'] : trans('install.contact_script_author');
			throw ValidationException::withMessages(['message' => $message]);
		}

		$product = isset($response['product']) ? $response['product'] : [];

		$next_release_build = isset($product['next_release_build']) ? $product['next_release_build'] : null;

		$is_downloaded = 0;
		if ($next_release_build) {
			if (\File::exists('../' . $next_release_build . '.zip')) {
				$is_downloaded = 1;
			}
		}

		if (isTestMode()) {
			$product['purchase_code'] = config('system.hidden_field');
			$product['email'] = config('system.hidden_field');
			$product['access_code'] = config('system.hidden_field');
			$product['checksum'] = config('system.hidden_field');

			$is_downloaded = 0;
		}

		return compact('about', 'product', 'update_tips', 'support_tips', 'is_downloaded');
	}

	public function helpDoc($subject = null) {
		if (!isConnected()) {
			throw ValidationException::withMessages(['message' => 'No internect connection.']);
		}

		$ac = \Storage::exists('.access_code') ? \Storage::get('.access_code') : null;
		$e = \Storage::exists('.account_email') ? \Storage::get('.account_email') : null;
		$c = \Storage::exists('.app_installed') ? \Storage::get('.app_installed') : null;
		$v = \Storage::exists('.version') ? \Storage::get('.version') : null;

		$url = config('app.helpdoc') . '/api/fc?s=' . $subject . '&u=' . url()->current() . '&ac=' . $ac . '&i=' . config('app.item') . '&e=' . $e . '&c=' . $c . '&v=' . $v;
		$response = curlIt($url);

		return isset($response['content']) ? $response['content'] : 'No content found.';
	}
}
