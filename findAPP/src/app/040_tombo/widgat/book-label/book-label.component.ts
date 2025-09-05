import { Component, Input } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';

@Component({
  selector: 'app-book-label',
  standalone: false,
  templateUrl: './book-label.component.html',
  styleUrl: './book-label.component.scss',
})
export class BookLabelComponent {
  @Input() data: any;
  @Input() editMode: boolean = false;
  show: boolean = false;
  formLabel: FormGroup;

  constructor(private fb: FormBuilder) {
    this.formLabel = this.fb.group({
      ln1: [''],
      ln2: [''],
      ln3: [''],
    });
  }

  onSubmit() {
    const dt = this.formLabel.value;
    console.log(dt);
  }

  ngOnChanges(): void {
    //Called before any other lifecycle hook. Use it to inject dependencies, but avoid any serious work here.
    //Add '${implements OnChanges}' to the class.
    this.formLabel.patchValue({
      ln1: this.data?.item?.i_ln1,
      ln2: this.data?.item?.i_ln2,
      ln3: this.data?.item?.i_ln3,
    });
  }

  ngOnInit() {
    if (this.data?.i_ln_1 != '') {
      this.show = true;
    } else {
      this.show = false;
    }
  }
}
