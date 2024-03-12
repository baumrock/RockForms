<?php

use Nette\Forms\Controls\CsrfProtection;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\UploadControl;
use Nette\Forms\Form;

return [
  CsrfProtection::PROTECTION => 'Ihre Sitzung ist abgelaufen. Bitte kehren Sie zur Startseite zurück und versuchen Sie es erneut.',
  Form::EQUAL => 'Bitte geben Sie %s ein.',
  Form::NOT_EQUAL => 'Dieser Wert darf nicht %s sein.',
  Form::FILLED => 'Dieses Feld ist erforderlich.',
  Form::BLANK => 'Dieses Feld muss leer sein.',
  Form::MIN_LENGTH => 'Bitte geben Sie mindestens %d Zeichen ein.',
  Form::MAX_LENGTH => 'Bitte geben Sie nicht mehr als %d Zeichen ein.',
  Form::LENGTH => 'Bitte geben Sie einen Wert zwischen %d und %d Zeichen ein.',
  Form::EMAIL => 'Bitte geben Sie eine gültige E-Mail Adresse ein.',
  Form::URL => 'Bitte geben Sie eine gültige URL ein.',
  Form::INTEGER => 'Bitte geben Sie eine gültige Ganzzahl ein.',
  Form::FLOAT => 'Bitte geben Sie eine gültige Zahl ein.',
  Form::MIN => 'Bitte geben Sie einen Wert ein, der größer oder gleich %d ist.',
  Form::MAX => 'Bitte geben Sie einen Wert kleiner oder gleich %d ein.',
  Form::RANGE => 'Bitte geben Sie einen Wert zwischen %d und %d ein.',
  Form::MAX_FILE_SIZE => 'Die Größe der hochgeladenen Datei kann bis zu %d Bytes betragen.',
  Form::MAX_POST_SIZE => 'Die hochgeladenen Daten überschreiten die Grenze von %d Bytes.',
  Form::MIME_TYPE => 'Die hochgeladene Datei hat nicht das erwartete Format.',
  Form::IMAGE => 'Die hochgeladene Datei muss ein Bild im Format JPEG, GIF, PNG oder WebP sein.',
  SelectBox::VALID => 'Bitte wählen Sie eine gültige Option aus.',
  UploadControl::VALID => 'Beim Hochladen der Datei ist ein Fehler aufgetreten.',
];
