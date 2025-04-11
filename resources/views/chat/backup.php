<tbody>
                                            @php $i = 1; @endphp
                                            @foreach ($users as $row)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $row->name }}</td>
                                                <td>{{ $row->position }}</td>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="invited_users[]" value="{{ $row->id }}" id="userCheckbox{{ $row->id }}">
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
