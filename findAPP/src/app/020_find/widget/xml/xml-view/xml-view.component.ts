import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-xml-view',
  standalone: false,
  templateUrl: './xml-view.component.html',
  styleUrl: './xml-view.component.scss',
})
export class XmlViewComponent {
  @Input() book: any = {};
  showDetails = false;
}
