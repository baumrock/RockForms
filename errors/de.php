<?php

use Nette\Forms\Controls\CsrfProtection;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\UploadControl;
use Nette\Forms\Form;

return [
  CsrfProtection::Protection => 'Ihre Sitzung ist abgelaufen. Bitte kehren Sie zur Startseite zurück und versuchen Sie es erneut.',
  Form::Equal => 'Bitte geben Sie %s ein.',
  Form::NotEqual => 'Dieser Wert darf nicht %s sein.',
  Form::Required => 'Dieses Feld ist erforderlich.',
  Form::Blank => 'Dieses Feld muss leer sein.',
  Form::MinLength => 'Bitte geben Sie mindestens %d Zeichen ein.',
  Form::MaxLength => 'Bitte geben Sie nicht mehr als %d Zeichen ein.',
  Form::Length => 'Bitte geben Sie einen Wert zwischen %d und %d Zeichen ein.',
  Form::Email => 'Bitte geben Sie eine gültige E-Mail Adresse ein.',
  Form::URL => 'Bitte geben Sie eine gültige URL ein.',
  Form::Integer => 'Bitte geben Sie eine gültige Ganzzahl ein.',
  Form::Float => 'Bitte geben Sie eine gültige Zahl ein.',
  Form::Min => 'Bitte geben Sie einen Wert ein, der größer oder gleich %d ist.',
  Form::Max => 'Bitte geben Sie einen Wert kleiner oder gleich %d ein.',
  Form::Range => 'Bitte geben Sie einen Wert zwischen %d und %d ein.',
  Form::MaxFileSize => 'Die Größe der hochgeladenen Datei kann bis zu %d Bytes betragen.',
  Form::MaxPostSize => 'Die hochgeladenen Daten überschreiten die Grenze von %d Bytes.',
  Form::MimeType => 'Die hochgeladene Datei hat nicht das erwartete Format.',
  Form::Image => 'Die hochgeladene Datei muss ein Bild im Format JPEG, GIF, PNG oder WebP sein.',
  SelectBox::Valid => 'Bitte wählen Sie eine gültige Option aus.',
  UploadControl::Valid => 'Beim Hochladen der Datei ist ein Fehler aufgetreten.',
];
