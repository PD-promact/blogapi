<script src="../../../Scripts/bootstrap.js"></script>
<script src="../../../Scripts/jquery-1.10.2.js"></script>
<div class='panel panel-primary'>
    <div class='panel-heading'>
        Manage Tags
    </div>
    <div class='panel-body'>
        <div class='table table-responsive'>
            <div style="padding-bottom:10px"><button class="btn btn-primary" (click)="addTag()">Add</button></div>            
           <div *ngIf='tags && tags.length==0' class="alert alert-info" role="alert">No record found!</div>
            <table class='table table-striped' *ngIf='tags && tags.length'>
                <thead>
                    <tr>
                        <th> Name</th>                        
                       <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr *ngFor="let tag of tags">
                        <td>{{tag.Name}}</td>                        
                       <td>
                            <button title="Edit" class="btn btn-primary" (click)="editTag(tag.Id)">Edit</button>
                            <button title="Delete" class="btn btn-danger" (click)="deleteTag(tag.Id)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div>
            </div>
        </div>
        <div *ngIf="msg" role="alert" class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">Error:</span>
            {{msg}}
        </div>
    </div>
</div>

<modal #modal>
    <form novalidate (ngSubmit)="onSubmit(tagFrm)" [formGroup]="tagFrm">
        <modal-header [show-close]="true">
            <h4 class="modal-title">{{modalTitle}}</h4>
        </modal-header>
        <modal-body>

           <div class="form-group">
                <div>
                    <span>Name*</span>
                    <input type="text" class="form-control" placeholder="Name" formControlName="Name">
                </div>              
            </div>
        </modal-body>
        <modal-footer>
            <div>
                <a class="btn btn-default" (click)="modal.dismiss()">Cancel</a>
                <button type="submit" [disabled]="tagFrm.invalid" class="btn btn-primary">{{modalBtnTitle}}</button>
            </div>
        </modal-footer>
    </form>
</modal>


import { Component, OnInit, ViewChild } from '@angular/core';
import { Service } from '../../Service/service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { ITag } from '../../Models/tag';
import { DBOperation } from '../../Shared/enum';
import { Global } from '../../Shared/global';

@Component({
    templateUrl: 'app/components/tag/tag.component.html'
})

export class TagComponent implements OnInit {
    @ViewChild('modal') modal: ModalComponent;
    tags: ITag[];
    tag: ITag;
    msg: string;
    indLoading: boolean = false;
    tagFrm: FormGroup;
    dbops: DBOperation;
    modalTitle: string;
    modalBtnTitle: string;

   constructor(private fb: FormBuilder, private _tagService: Service) { }

   ngOnInit(): void {

       this.tagFrm = this.fb.group({
            Id: [''],
            Name: ['', Validators.required]
        });

       this.LoadTags();
    }
    LoadTags(): void {
        this.indLoading = true;
        this._tagService.get(Global.BASE_API_ENDPOINT + 'Tags/')
            .subscribe(tags => { this.tags = tags; this.indLoading = false; },
            error => this.msg = <any>error);
    }

   addTag() {
        this.dbops = DBOperation.create;
        this.SetControlsState(true);
        this.modalTitle = 'Add New Tag';
        this.modalBtnTitle = 'Add';
        this.tagFrm.reset();
        this.modal.open();
    }

   editTag(id: number) {
        this.dbops = DBOperation.update;
        this.SetControlsState(true);
        this.modalTitle = 'Edit Tag';
        this.modalBtnTitle = 'Update';
        this.tag = this.tags.filter(x => x.Id === id)[0];
        this.tagFrm.setValue(this.tag);
        this.modal.open();
    }

   deleteTag(id: number) {
        this.dbops = DBOperation.delete;
        this.SetControlsState(false);
        this.modalTitle = 'Confirm to Delete?';
        this.modalBtnTitle = 'Delete';
        this.tag = this.tags.filter(x => x.Id === id)[0];
        this.tagFrm.setValue(this.tag);
        this.modal.open();
    }

   SetControlsState(isEnable: boolean) {
        isEnable ? this.tagFrm.enable() : this.tagFrm.disable();
    }

   onSubmit(formData: any) {
        this.msg = "";


[10:54] 
switch (this.dbops) {
            case DBOperation.create:
                this._tagService.post(Global.BASE_API_ENDPOINT + 'AddTags/', formData._value).subscribe(
                    data => {
                        if (data === 1) // Success
                        {
                            this.msg = "Data successfully added.";
                            this.LoadTags();
                        }
                        else {
                            this.msg = "There is some issue in saving records, please contact to system administrator!";
                            this.LoadTags();
                        }

                       this.modal.dismiss();
                    },
                    error => {
                        this.msg = error;
                    }
                );
                break;
            case DBOperation.update:
                this._tagService.put(Global.BASE_API_ENDPOINT + 'PutTag/', formData._value.Id, formData._value).subscribe(
                    data => {
                        if (data === 1) // Success
                        {
                            this.msg = "Data successfully updated.";
                            this.LoadTags();
                        }
                        else {
                            this.msg = "There is some issue in saving records, please contact to system administrator!";
                            this.LoadTags();
                        }

                       this.modal.dismiss();
                    },
                    error => {
                        this.msg = error;
                    }
                );
                break;
            case DBOperation.delete:
                this._tagService.delete(Global.BASE_API_ENDPOINT + 'DeleteTag/', formData._value.Id).subscribe(
                    data => {
                        if (data === 1) // Success
                        {
                            this.msg = "Data successfully deleted.";
                            this.LoadTags();
                        }
                        else {
                            this.msg = "There is some issue in saving records, please contact to system administrator!";
                            this.LoadTags();
                        }

                       this.modal.dismiss();
                    },
                    error => {
                        this.msg = error;
                    }
                );
                break;
        }
    }

}

export interface ITag {
    Id: number,
    Name: string          
}