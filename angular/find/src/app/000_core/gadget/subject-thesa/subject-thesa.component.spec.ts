import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SubjectThesaComponent } from './subject-thesa.component';

describe('SubjectThesaComponent', () => {
  let component: SubjectThesaComponent;
  let fixture: ComponentFixture<SubjectThesaComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [SubjectThesaComponent]
    });
    fixture = TestBed.createComponent(SubjectThesaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
