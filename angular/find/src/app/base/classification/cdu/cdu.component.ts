import { Component } from '@angular/core';

@Component({
  selector: 'app-cdu',
  templateUrl: './cdu.component.html',
  styleUrls: ['./cdu.component.scss']
})
export class CduComponent {
  tag = "020";
  cutter = "R685J";
  year = "1969";
  imagePath = '/assets/img/item_cdu.png';
}
