import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-classification-colors',
  templateUrl: './colors.component.html',
  styleUrls: ['./colors.component.scss']
})
export class ColorsComponent {
  @Input() public code:Array<any> | any
}
