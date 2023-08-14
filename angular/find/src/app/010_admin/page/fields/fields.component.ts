import { FindService } from 'src/app/000_core/service/find.service';
import { FormBuilder, FormControl, FormGroup } from '@angular/forms';
import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-rdf-fields-form',
  templateUrl: './fields.component.html',
  styleUrls: ['./fields.component.scss']
})
export class FieldsComponent {
  public class:string = ''
  public label:string = ''
  public form:string = ''
  public prop:string = ''
  public createRDF:string = 'd-none';
  public idRec: string = '';

  public items:Array<any> | any
  public data:Array<any> | any

  public groups:Array<any> = [
      {name:'Autor',type:'hasAuthor',class:'Person',form:'rdf'},
      {name:'Organizador',type:'organization',class:'Person',form:'rdf'},
      {name:'Ilustrador',type:'illustrator',class:'Person',form:'rdf'},
      {name:'Tradutor',type:'translator',class:'Person',form:'rdf'},
      {name:'Editora',type:'isPublisher',class:'Person',form:'rdf'},
      {name:'Publicacao',type:'description',class:'Person',form:'rdf'},
      {name:'Ano publicação',type:'dateOfPublication',class:'Person',form:'rdf'},
      {name:'Idioma',type:'hasLanguageExpression',class:'Person',form:'rdf'},
      {name:'Paginas',type:'hasPage',class:'Person',form:'rdf'},
      {name:'Edição',type:'isEdition',class:'Person',form:'rdf'},

  ];
  @Input() public isbn:string = '';
  @Input() public resource:string = '';

  constructor(
    private formBuilder:FormBuilder,
    private findService:FindService
  ) {}

  rdfForm = new FormGroup({
      term: new FormControl(''),
  });

  selectForm = new FormGroup({
      id: new FormControl(''),
  });

  saveRDF()
    {
      this.saveRDFData(this.resource,this.prop,this.idRec,'');
    }

  saveRDFData(r1:string,prop:string,r2:string,lit:string)
    {
        this.findService.saveRDF(r1,prop,r2,lit).subscribe(
          res=>{
            console.log(res);

          }
        )
    }

  createRDFItem()
    {
        let name = this.rdfForm.value.term as string;
        alert(name);
        alert(this.class);
        this.findService.createConcept(name,this.class).subscribe(
          res=>{
            console.log(res);
          }
        )
    }

  area(type:string,label:string,form:string,prop:string)
    {
      this.label = label
      this.class = prop
      this.form = form
      this.prop = type
      this.items = []
      this.createRDF = 'd-none';
    }

  onChange($event:any) {
    this.idRec = $event.target.value;
  }

    onKeyUp(event: any)
    {
      let term = this.rdfForm.value.term as string
      console.log(event);
      console.log(term)
      console.log(term?.length)
      if (term.length > 3)
        {
            this.findService.search(term,this.class).subscribe(
              res=>{
                this.data = res;
                this.items = this.data.data
                console.log(this.items.length);
                if (this.items.length ==0)
                  {
                      this.createRDF = '';
                  }
              }
            )
            console.log('Pesquisar '+term)
        } else {
          this.items = []
          this.createRDF = 'd-none';
        }
    }
}
