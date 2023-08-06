import { FindService } from 'src/app/000_core/service/find.service';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-item-add-isbn',
  templateUrl: './item-add-isbn.component.html',
  styleUrls: ['./item-add-isbn.component.scss']
})
export class ItemAddIsbnComponent {
  constructor(
    private fb: FormBuilder,
    private findService: FindService
  ) { }

  @Input() public isbn: string = '';
  @Input() public book: Array<any> | any;

  tomboForm = new FormGroup({
    bl_tombo: new FormControl('1', Validators.required),
    bl_place: new FormControl('', Validators.required),
  });
  place: Array<any> | any

  ngOnInit() {
    this.findService.getPlace().subscribe(
      res => {
        this.place = res
        let lp = this.place[0].id_lp
        let tombo = this.tomboForm.value.bl_tombo
        console.log(res)
        if (res.length === 1) {
          this.tomboForm.setValue({
            bl_place: String(lp),
            bl_tombo: String(tombo)
          })
        }
        console.log('++++++++' + lp)
      }
    );
  }

  newItem() {
    let isbn = this.isbn;
    let place = this.tomboForm.value.bl_place as string
    let tombo = this.tomboForm.value.bl_tombo as string
    this.findService.register_item(isbn, place, tombo).subscribe(
      res => {
        this.book = res;
      }
    )
  }
}
