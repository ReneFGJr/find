import { Component } from '@angular/core';

@Component({
  selector: 'app-cdd',
  templateUrl: './cdd.component.html',
  styleUrls: ['./cdd.component.scss']
})
export class CddComponent {
    tag = "020";
    cutter = "R685J";
    year = "1969";
    imagePath = '/assets/img/item_cdd.png';
}
