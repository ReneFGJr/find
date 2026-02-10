import { Component, Input } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { FindService } from '../../../010_core/service/find.service';

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
  msg: string = ''
  rsp: any = [];

  constructor(
    private fb: FormBuilder,
    private findService: FindService
  ) {
    this.formLabel = this.fb.group({
      ln1: [''],
      ln2: [''],
      ln3: [''],
    });
  }

  onChange() {
    let dt = { 
      tomboID: this.data?.data?.tomboID,
      library: this.data?.data?.library, 
      ln1: this.formLabel.value.ln1,
      ln2: this.formLabel.value.ln2,
      ln3: this.formLabel.value.ln3
    };

    this.findService
      .api_post('catalog/label/update', dt)
      .subscribe(
        (res) => {
          this.rsp = res;
          this.msg = this.rsp?.msg;

          // ⏱ limpa a mensagem após 5 segundos
          setTimeout(() => {
            this.msg = '';
          }, 5000);          
        },
        (error) => {
          console.error('Error updating label', error);
        }
      );    

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
