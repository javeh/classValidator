# Journal

- [x] Message-Handling vereinheitlicht: zentrale Message-Hilfsfunktionen ergänzt und alle Validatoren so angepasst, dass benutzerdefinierte Meldungen nicht mehr überschrieben werden.
- [x] Konstruktor-Signaturen vereinheitlicht und Sprachstil der Fehlermeldungen (u. a. Text-/Längen-Validatoren) harmonisiert.
- [x] Typ- und Nullability-Regeln definiert: Dokumentation ergänzt und sämtliche Validatoren so angepasst, dass `null` standardmäßig übersprungen und Typfehler konsistent behandelt werden.
- [x] Text-/Length-Zuständigkeiten geklärt: Text nutzt nun intern den Length-Validator und behält seine Parameter, wodurch beide Klassen dieselbe Längenlogik teilen.
- [x] Zusätzliche Parameter-Validierungen ergänzt (Regex-Pattern, Number step/min/max, Date-Format) und im Regelwerk dokumentiert.
