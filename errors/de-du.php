<?php

use Nette\Forms\Controls\CsrfProtection;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\UploadControl;
use Nette\Forms\Form;

return [
  CsrfProtection::Protection => 'Deine Sitzung ist abgelaufen. Bitte kehre zur Startseite zurück und versuche es erneut.',
  Form::Equal => 'Bitte gib %s ein.',
  Form::NotEqual => 'Dieser Wert darf nicht %s sein.',
  Form::Required => 'Dieses Feld ist erforderlich.',
  Form::Blank => 'Dieses Feld muss leer sein.',
  Form::MinLength => 'Bitte gib mindestens %d Zeichen ein.',
  Form::MaxLength => 'Bitte gib nicht mehr als %d Zeichen ein.',
  Form::Length => 'Bitte gib einen Wert zwischen %d und %d Zeichen ein.',
  Form::Email => 'Bitte gib eine gültige E-Mail Adresse ein.',
  Form::URL => 'Bitte gib eine gültige URL ein.',
  Form::Integer => 'Bitte gib eine gültige Ganzzahl ein.',
  Form::Float => 'Bitte gib eine gültige Zahl ein.',
  Form::Min => 'Bitte gib einen Wert ein, der größer oder gleich %d ist.',
  Form::Max => 'Bitte gib einen Wert kleiner oder gleich %d ein.',
  Form::Range => 'Bitte gib einen Wert zwischen %d und %d ein.',
  Form::MaxFileSize => 'Die Größe der hochgeladenen Datei kann bis zu %d Bytes betragen.',
  Form::MaxPostSize => 'Die hochgeladenen Daten überschreiten die Grenze von %d Bytes.',
  Form::MimeType => 'Die hochgeladene Datei hat nicht das erwartete Format.',
  Form::Image => 'Die hochgeladene Datei muss ein Bild im Format JPEG, GIF, PNG oder WebP sein.',
  SelectBox::Valid => 'Bitte wähle eine gültige Option aus.',
  UploadControl::Valid => 'Beim Hochladen der Datei ist ein Fehler aufgetreten.',
];
