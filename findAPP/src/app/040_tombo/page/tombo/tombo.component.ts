import { FindService } from './../../../010_core/service/find.service';
import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-tombo',
  standalone: false,
  templateUrl: './tombo.component.html',
  styleUrl: './tombo.component.scss',
})
export class TomboComponent {
  form: FormGroup;
  loading = false;
  error: string | null = null;
  data: Array<any> | any;

  constructor(
      private fb: FormBuilder,
      private findService: FindService) {
    this.form = this.fb.group({
      tombo: ['', [Validators.required, Validators.pattern(/^\d+$/)]],
    });
  }

  onSubmit() {
    if (this.form.invalid) {
      return;
    }

    this.loading = true;
    this.error = null;
    const tombo = this.form.value.tombo;
    const dt = {
      tomboID: tombo
    };

    console.log("DT===",dt);

    this.findService.api_post('tombo/v',dt).subscribe({
      next: (res) => {
        this.loading = false;
        this.data = res;

        console.log(res);
      },
      error: (err) => {
        this.loading = false;
        this.error = 'Não foi possível realizar a consulta.';
        console.error(err);
      },
    });
  }
}
