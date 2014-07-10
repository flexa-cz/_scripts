<?php
class RegistrationFormPragodata{
	private $action;
	private $subactions;

	/* ************************************************************************ */
	/* public methods */
	/* ************************************************************************ */
	public function printForm(){
		$this->isReady();
		$return='
			<h1>
				'.$this->action['nazev'].'
				- registrační formulář<br />
				<em class="prihl_en">Registration form</em>
			</h1>
			<form id="register_form" method="post" action="">
			'.$this->printInvoiceForm().'
			'.$this->printPersonForm().'
			'.$this->printSummaryForm().'
			</form>';
		return $return;
	}

	public function setAction(array $action){
		$this->action=$action;
		return $this;
	}

	public function setSubactions(array $subactions){
		$this->subactions=$subactions;
		return $this;
	}

	/* ************************************************************************ */
	/* private methods */
	/* ************************************************************************ */

	private function isReady(){
		if(empty($this->action)){
			throw new Exception('Didnt set parameter "action"!');
		}
		elseif(empty($this->subactions)){
			throw new Exception('Didnt set parameter "subactions"!');
		}
		return $this;
	}

	private function printSummaryForm(){
		$return='
			<fieldset>
				<ul class="form_list">
					<li class="form_list_node">
						 <label class="form_label" for="zprava">
							 Zpráva pro organizátory:<br />
							 <span class="form_label_trans">Note:</span>
						 </label>
						 <textarea width="30" height="10" name="zprava" id="zprava" class="form_textarea"></textarea>
					 </li>

					<li class="form_list_node">
						 <label class="form_label" for="souhlas">
							 Souhlasím s podmínkami registrace:<br />
							 <span class="form_label_trans">I accept the conditions:</span>
						 </label>
						 <input type="checkbox" name="souhlas" id="souhlas" class="form_checkbox" value="1">
					 </li>

					<li class="form_list_node">
						 <label class="form_label">
							 Cena objednávky:<br />
							 <span class="form_label_trans">The price of registration:</span>
						 </label>
						 <strong><span id="final_sum">0</span> <span id="final_currency">Kč</span> včetně DPH</strong>
					 </li>

					<li class="form_list_node">
						 <label class="form_label">
							 Odeslat<br />
							 <span class="form_label_trans">Submit</span>
						 </label>
						 <input type="text" name="email" class="hide" />
						 <input type="hidden" name="akce_id" value="'.$this->action['id'].'" />
						 <input type="hidden" name="final_sum" id="odeslani_cena" value="0" />
						 <input type="hidden" name="count_of_participants" id="pocet" value="0" />
						 <input type="submit" name="odeslat" id="odeslat" value="Odeslat/Submit" />
					 </li>
				</ul>
			</fieldset>
			';
		return $return;
	}

	private function printPersonForm(){
		$return='
			<h1>
			Seznam přihlášených účastníků<br />
			<em class="prihl_en">List of participants</em>
			</h1>
			';
		$return.='
			<table>
				<tr>';
		$return.='<th>#</th>';
		$return.='<th>Jméno a příjmení<br /><em class="prihl_en">Full name</em></th>';
		$return.='<th>E-mail</th>';
		$return.='<th>Student&nbsp;*</th>';
		foreach($this->subactions as $subaction){
			$return.='<th>'.$subaction['nazev'].'</th>';
		}
		$return.='<th>Odebrat<br /><em class="prihl_en">Remove</em></th>';
		$return.='
				</tr>
				<tr class="participant" id="code-segment-temp">';
		$return.='<th>#list#</th>';
		$return.='<td><input type="text" name="jm[#num#]" class="WidthAuto" /></td>';
		$return.='<td><input type="text" name="em[#num#]" class="WidthAuto" /></td>';
		$return.='<td><input type="checkbox" name="st[#num#]" class="show_hide_student_price" /></td>';
		foreach($this->subactions as $subaction){
			$return.='<td>';
			$return.='<label>';
			$return.='<input type="checkbox" name="podakce[#num#]['.$subaction['id'].']">';
			$return.='<span style="display: none;" class="price czk normal_price" rel="'.$subaction['price_czk'].'">'.$subaction['price_czk'].'&nbsp;Kč</span>';
			$return.='<span style="display: none;" class="price eur normal_price" rel="'.$subaction['price_eur'].'">'.$subaction['price_eur'].'&nbsp;&euro;</span>';
			$return.='<span style="display: none;" class="price czk student_price" rel="'.$subaction['price_student_czk'].'">'.$subaction['price_student_czk'].'&nbsp;Kč</span>';
			$return.='<span style="display: none;" class="price eur student_price" rel="'.$subaction['price_student_eur'].'">'.$subaction['price_student_eur'].'&nbsp;&euro;</span>';
			$return.='</label>';
			$return.='</td>';
		}
		$return.='<td><a href="#" class="code-segment-remove button cross">Odebrat účastníka</a></td>';
		$return.='
				</tr>
				<tbody id="code-segment-root">
				</tbody>
			</table>
			<a href="#" class="code-segment-add button add">Přidat účastníka</a>
			';
		return $return;
	}

	private function printInvoiceForm(){
		$return='
		<fieldset>
		<legend>Fakturační údaje</legend>
		<span class="form_legend_trans">Invoice data</span>
		<ul class="form_list">
			<li class="form_list_node">
				<label class="form_label" for="jmeno">
					Organizace (nebo Vaše jméno):<br />
					<span class="form_label_trans">Institution (or your name):</span>
				</label>
				<input class="form_input" type="text" name="jmeno" id="jmeno" maxlength="250">
			</li>

			<li class="form_list_node">
				<label class="form_label" for="ulice">
					Ulice a číslo:<br />
					<span class="form_label_trans">Address:</span>
				</label>
				<input class="form_input" type="text" name="ulice" id="ulice" maxlength="250">
			</li>

			<li class="form_list_node">
				<label class="form_label" for="mesto">
					Obec:<br />
					<span class="form_label_trans">City:</span>
				</label>
				<input class="form_input" type="text" name="mesto" id="mesto" maxlength="250">
			</li>

			<li class="form_list_node">
				<label class="form_label" for="psc">
					PSČ:<br />
					<span class="form_label_trans">Postal code:</span>
				</label>
				<input class="form_input" type="text" name="psc" id="psc" maxlength="250">
			</li>

			<li class="form_list_node">
				<label class="form_label" for="cr">
					Příslušnost:<br />
					<span class="form_label_trans">Nationality:</span>
				</label>
				<select name="cr" id="cr" class="show_hide_rows form_select" rel="nationality">
					<option value="1" rel="hide">ČR</option>
					<option value="2" rel="show">cizinec/foreigner</option>
				</select>
			</li>

			<li class="form_list_node nationality">
				<label class="form_label" for="stat">
					Stát:<br />
					<span class="form_label_trans">Country:</span>
				</label>
				<input class="form_input" type="text" name="stat" id="stat">
			</li>

			<li class="form_list_node nationality">
				<label class="form_label" for="mena">
					Měna:<br />
					<span class="form_label_trans">Currency:</span>
				</label>
				<select name="mena" id="mena" class="show_hide_currency form_select">
					<option value="1" rel="czk">Kč</option>
					<option value="2" rel="eur">&euro;</option>
				</select>
			</li>

			<li class="form_list_node">
				<label class="form_label" for="osoba">
					Osoba:<br />
					<span class="form_label_trans">Company:</span>
				</label>
				<select name="osoba" id="osoba" class="show_hide_rows form_select" rel="personality">
					<option value="1" rel="hide">soukromá/personal</option>
					<option value="2" rel="show">právnická/škola/OSVČ/company</option>
				</select>
			</li>

			<li class="form_list_node personality">
				<label class="form_label" for="ic">
					IČ:<br />
					<span class="form_label_trans">Identification No.:</span>
				</label>
				<input class="form_input" type="text" name="ic" maxlength="250" id="ic">
			</li>

			<li class="form_list_node personality">
				<label class="form_label" for="dic">
					DIČ:<br />
					<span class="form_label_trans">VAT RN:</span>
				</label>
				<input class="form_input" type="text" name="dic" maxlength="250">
			</li>

			<li class="form_list_node">
				<label class="form_label" for="kontakt">
					Kontaktní osoba pro komunikaci o platbě:<br />
					<span class="form_label_trans">Contact person:</span>
				</label>
				<input class="form_input" type="text" name="kontakt" id="kontakt" maxlength="250">
			</li>

			<li class="form_list_node">
				<label class="form_label" for="k-mail">E-mail:</label>
				<input class="form_input" type="text" name="k-mail" id="k-mail" maxlength="250">
			</li>


			<li class="form_list_node">
				<label class="form_label" for="telefon">
					Telefon:<br />
					<span class="form_label_trans">Phone:</span>
				</label>
				<input class="form_input" type="text" name="telefon" id="telefon" maxlength="250">
			</li>

		</ul>
		</fieldset>';
		return $return;
	}
}