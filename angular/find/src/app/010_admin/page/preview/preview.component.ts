import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-preview',
  templateUrl: './preview.component.html',
  styleUrls: ['./preview.component.scss']
})
export class PreviewComponent {
  edit_title:boolean = false;
  @Input() public book:Array<any>|any

  edit(field:string)
    {
      if (field=='title')
        {
          this.edit_title = true;
        }
    }

  save(field: string) {
    if (field == 'title') {
      this.edit_title = false;
    }
  }

  cancel(field: string) {
    if (field == 'title') {
      this.edit_title = false;
    }
  }

}
