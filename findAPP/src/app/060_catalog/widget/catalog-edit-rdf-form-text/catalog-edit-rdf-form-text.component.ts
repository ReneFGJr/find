import { Component, EventEmitter, Input, Output } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { FindService } from '../../../010_core/service/find.service';

@Component({
  selector: 'app-catalog-edit-rdf-form-text',
  standalone: false,
  templateUrl: './catalog-edit-rdf-form-text.component.html',
  styleUrl: './catalog-edit-rdf-form-text.component.scss',
})
export class CatalogEditRdfFormTextComponent {
  @Input() data: any;
  @Output() action = new EventEmitter<string>();
  formAction!: FormGroup;

  constructor(private fb: FormBuilder, private findService: FindService) {}

  ngOnInit() {
    this.formAction = this.fb.group({
      caption: [this.data?.Caption || '', Validators.required],
    });
  }

  cancel(){
    this.action.emit('canceled');
  }

  save() {
    if (this.formAction.invalid) return;

    this.data.Caption = this.formAction.value.caption;
    let dt = {
      id: this.data.ID,
      value: this.data.Caption,
      action: 'update',
    };
    this.findService
      .api_post('rdf/literal/' + this.data.IdN, dt)
      .subscribe((response) => {
        this.action.emit('saved');
      });
  }
}
