import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-google-search',
  templateUrl: './search.component.html',
  styleUrls: ['./search.component.scss']
})
export class SearchGoogleComponent {
  @Input() public term:string = '';
}
