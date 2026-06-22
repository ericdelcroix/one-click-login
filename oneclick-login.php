<?php
/** 
 * Display a list of predefined database servers to login with just one click.
 * Don't use this in production environment unless the access is restricted
 *
 * @link https://www.adminer.org/plugins/#use
 * @author Gio Freitas, https://www.github.com/giofreitas
 * @author Eric Delcroix, https://www.github.com/ericdelcroix
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class OneClickLogin {
	/** @access protected */
	public $servers, $driver;

	/**
	 * Normalize one host entry to a list of login profiles.
	 * Backward compatible formats:
	 * - host => [username, pass, label?, databases?]
	 * - host => [profiles => [[username, pass, ...], ...]]
	 * - host => [[username, pass, ...], [username, pass, ...]]
	 */
	protected function hostProfiles($host, $server) {
		$profiles = [];

		if (isset($server['profiles']) && is_array($server['profiles'])) {
			$profiles = $server['profiles'];
		} elseif (isset($server[0]) && is_array($server[0])) {
			$profiles = $server;
		} else {
			$profiles = [$server];
		}

		$normalized = [];
		foreach ($profiles as $profile) {
			if (!is_array($profile)) {
				continue;
			}

			$profileHost = $profile['host'] ?? $host;
			$profile['host'] = $profileHost;
			$normalized[] = $profile;
		}

		return $normalized;
	}
	
	/** 
	 * Set supported servers
	 * @param array $servers
	 * @param string $driver
	 */
	public function __construct($servers, $driver = "server") {
		$this->servers = $servers;
		$this->driver = $driver;
	}

	public function login($login, $password) {
		return isset($this->servers[Adminer\SERVER]);
	}
	
	public function databaseValues($server){
		$databases = $server['databases'];
		if(is_array($databases))
			foreach($databases as $database => $name){
				if(is_string($database))
					continue;
				unset($databases[$database]);
				if(!isset($databases[$name]))
					$databases[$name] = $name;
			}
		return $databases;
	}
	
	public function loginForm() {
		?>
		</form>
		<style>
			tr.no-stripe td,
			tr.no-stripe th {
			background-color: lightgray !important;
			border: none !important;
			}
			tr.stripe td,
			tr.stripe th {
			background-color: #f8f8f8 !important;
			border: none !important;
			}
			tr.profile-start td {
			border-top: 2px solid #dcdcdc;
			padding-top: 6px;
			}
			table#one-click-login thead th {
			background-color: #3d6291 !important;
			color: #fff !important;
			font-weight: bold;
			letter-spacing: 0.04em;
			padding: 6px 8px;
			border: none !important;
			}
		</style>
		<table id="one-click-login">
			<thead>
			<tr>
				<th style="text-align: left;"><?= Adminer\lang('Server') ?></th>
				<th style="text-align: left;"><?= Adminer\lang('Username') ?></th>
				<th style="text-align: left;"><?= Adminer\lang('Database') ?></th>
				<th style="text-align: left;"><?= Adminer\lang('Action') ?></th>
			</tr>
			</thead>
			<tbody>
			
			<?php
			$j=0;
			foreach($this->servers as $host => $server):
				foreach($this->hostProfiles($host, $server) as $profile):
					$databases = $profile['databases'] ?? "";
					$j++;
					if (!is_array($databases)) {
						$databases = [$databases => $databases];
					}

					foreach(array_keys($databases) as $i => $database):
						$rowClass = ($j % 2 === 0 ? "no-stripe" : "stripe") . ($i === 0 && $j > 1 ? " profile-start" : "");
						?>
						<tr class="<?= $rowClass ?>">
							<?php if( $i === 0): ?>
								<td style="vertical-align:middle" rowspan="<?= count($databases) ?>"><?= isset($profile['label']) ? "{$profile['label']} ({$profile['host']})" : $profile['host']; ?></td>
								<td style="vertical-align:middle" rowspan="<?= count($databases) ?>"><?= $profile['username'] ?></td>
							<?php endif; ?>
							<td style="vertical-align:middle"><?= $databases[$database] ?></td>
							<td>
								<form action="" method="post">
									<input type="hidden" name="auth[driver]" value="<?= $this->driver; ?>">
									<input type="hidden" name="auth[server]" value="<?= $profile['host']; ?>">
									<input type="hidden" name="auth[username]" value="<?= Adminer\h($profile['username']); ?>">
									<input type="hidden" name="auth[password]" value="<?= Adminer\h($profile['pass']); ?>">
									<input type='hidden' name="auth[db]" value="<?= Adminer\h($database); ?>"/>
									<input type='hidden' name="auth[permanent]" value="1"/>
									<input type="submit" value="<?= Adminer\lang('Enter'); ?>">
								</form>
							</td>
						</tr>
						<?php
					endforeach;
				endforeach;
			endforeach;	
			?>
			</tbody>
		</table>	
		<form action="" method="post">		
		<?php
		return true;
	}
	
}

### END OF FILE
