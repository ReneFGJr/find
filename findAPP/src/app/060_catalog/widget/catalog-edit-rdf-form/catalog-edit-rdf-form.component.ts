import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component({
  selector: 'app-catalog-edit-rdf-form',
  standalone: false,
  templateUrl: './catalog-edit-rdf-form.component.html',
  styleUrl: './catalog-edit-rdf-form.component.scss',
})
export class CatalogEditRdfFormComponent {
  @Input() data: any;
  @Output() action = new EventEmitter<string>();

  handleAction(event: any) {
    this.action.emit("saved");
  }
}
